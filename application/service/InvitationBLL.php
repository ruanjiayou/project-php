<?php
use \Firebase\JWT\JWT;
use think\Request;

class InvitationBLL extends BLL {

  public $table = 'invitation';
  
  /**
   * 卖家是否可以被邀请,最后一单要被买家评价或过了2小时,在工作日内
   * @param $sellerId 卖家id
   * @param $workAt 工作日期
   */
  function canInvited($sellerId, $date) {
    list($d,$t) = explode(' ', $date);
    $isWork = (new UserWorkBLL())->isWork(['userId'=>$sellerId, 'workAt'=>$d]);
    if(false === $isWork) {
      return false;
    }
    $user = (new UserBLL())->getInfo($sellerId);
    if($user['workWill'] == 0) {
      thrower('invitation', 'willless');
    }
    $lastInvitation = $this->getInfo(['sellerId'=>$sellerId],['order'=> 'id DESC']);
    // 最后一单: 成功但没success(confirmed) 表示在工作中
    // if(!empty($lastInvitation) && $lastInvitation['status'] === 'success' && $lastInvitation['progress'] === 'confirmed') {
    //   return false;
    // }
    // $now = time();
    // $yet = $now - 68400;
    // $hql = ['where'=>[
    //   'sellerId'=>$sellerId, 'progress'=>'canceling', 'canceledAt'=>['between',[date('Y-m-d H:i:s', $yet),date('Y-m-d H:i:s', $now)]]
    // ]];
    // $lastTwo = $this->getList($hql);
    // if($lastTwo['count']>2) {
    //   thrower('invitation', 'canceledTooMany');
    // }
    return true;
  }
  /**
   * 发出邀请
   * 1.查询上级和价格数据
   * 2.卖家可以被邀请
   * 4.买家钱够
   * 5.发送消息
   */
  function invite($user, $input) {
    if($user['type']!=='buyer') {
      throw new Error('本用户类型没有此项权限!');
    }
    $validation = new Validater([
      'userId' => 'required|int|alias:sellerId',
      'priceId' => 'required|int|alias:price',
      'x' => 'required|float:10,6',
      'y' => 'required|float:10,6',
      'address' => 'required|string',
      'startAt' => 'required|date',
      'duration' => 'int',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    $canWork = $this->canInvited($data['sellerId'], $data['startAt']);
    if($canWork === false) {
      thrower('invitation', 'userDontWork');
    }
    $sellerrccode = (new RccodeBLL())->getInfo(['userId'=>$data['sellerId']]);
    $buyerrccode = (new RccodeBLL())->getInfo(['userId'=>$user['id']]);
    $data['sellerAgencyId'] = $sellerrccode['agencyId'];
    $data['buyerAgencyId'] = $buyerrccode['agencyId'];
    $price = (new PriceBLL())->getInfo($data['price']);
    $data['price'] = $price['value'];
    list($d,$t) = explode(' ', $data['createdAt']);
    if(false === _::isBefore($d.' 00:00:00', $data['startAt'])) {
      thrower('invitation', 'dateInvalid');
    }
    
    if($data['price'] > $user['money']) {
      thrower('user', 'moneyLess');
    }
    $seller = (new UserBLL())->getInfo($data['sellerId']);
    if(null === $seller || $seller['status']!=='approved') {
      thrower('user', 'userNotFound');
    }
    // 佣金计算
    $data['rebate'] = round($seller['rebate']*$data['price']/100);
    $rebatePrice = (new PriceBLL())->getInfo(['type'=>'rebate']);
    $data['rebateAgency'] = round($data['price']*intval($rebatePrice['value'])/100);
    $data['buyerId'] = $user['id'];
    $data['buyerName'] = $user['nickName'];
    $data['buyerAvatar'] = $user['avatar'];
    $data['buyerPhone'] = $user['phone'];
    $data['sellerName'] = $seller['nickName'];
    $data['sellerAvatar'] = $seller['avatar'];
    $data['sellerPhone'] = $seller['phone'];
    (new SmsMessageBLL())->sendMessage([
      'phone' => $data['sellerPhone'],
      'type' => 'invite',
      'params' => [$data['sellerName']]
    ]);
    return model($this->table)->add($data);
  }

  /**
   * 1.判断角色的操作是否符合
   * 2.记录存在
   * 3.进度:refused/accepted/canceling/canceled/comfirmed/success(定时器或评论中有此状态)
   * 4.取消邀请,超过指定时间则扣钱给卖家.卖家是不扣钱的,顶多限时接单
   */
  public function changeProgress($invitatoinId, $status, $user) {
    $userBillBLL = new UserBillBLL();
    $smsMesageBLL = new SmsMessageBLL();
    if($user['type'] !== 'buyer' && $status === 'canceling' || $user['type']!=='servant' && in_array($status, ['refused','canceled','accepted','comfirmed'])) {
      throw new Exception('本用户类型没有此项权限!');
    }
    $invitation = self::getInfo($invitatoinId);
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    $buyer = null;
    $seller = null;
    if($user['type'] === 'buyer') {
      $buyer = $user;
      $seller = (new UserBLL())->getInfo($invitation['sellerId']);
    } else {
      $seller = $user;
      $buyer = (new UserBLL())->getInfo($invitation['buyerId']);
    }
    // accepted状态保持pending,confirmed改为success,其他都是fail
    $input = ['progress'=>$status];
    $progress = $invitation['progress'];
    // pending: inviting accepted
    // success: comfirmed
    // fail: refused canceling canceled expired 
    if('refused' === $status) {
      if($progress !== 'inviting') {
        thrower('invitation', 'updateFail', '只能取消邀请中状态的邀请!');
      } else {
        $input['status']='fail';
        $smsMesageBLL->sendMessage([
          'phone' => $buyer['phone'],
          'type' => 'refused',
          'cid' => $buyer['cid'],
          'params' => [$buyer['nickName']]
        ]);
      }
    } elseif('accepted' === $status) {
      if('inviting' !== $progress) {
        thrower('invitation', 'updateFail', '只能接受邀请中状态的邀请!');
      } else {
        unset($input['status']);
        // 接受邀请扣钱
        if(null === $buyer) {
          thrower('user', 'userNotFound');
        }
        if($buyer['money'] < $invitation['price']) {
          thrower('user', 'moneyLess');
        } else {
          $userBillBLL->balance([
            'type' => 'expent',
            'value' => $invitation['price'],
            'detail' => '接受邀请,扣钱'
          ], $buyer);
        }
      }
      $input['acceptedAt'] = date('Y-m-d H:i:s');
      // 接受邀请订单, 给A发送
      $smsMesageBLL->sendMessage([
        'phone' => $seller['phone'],
        'type' => 'accepted2A',
        'cid' => $seller['cid'],
        'params' => [$seller['nickName'], $invitation['startAt']]
      ]);
      // 接受邀请订单, 给C发送
      $smsMesageBLL->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'accepted2C',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName']]
      ]);
    } elseif('canceling' === $status) {
      $input['status']='fail';
      $input['canceledAt'] = date('Y-m-d H:i:s');
      if($progress === 'accepted') {
        // 按时间扣钱
        $current = time();
        $t = strtotime($invitation['createdAt']);
        $punishment_money = 0;
        if($current > 60*C_PUNISHMENT2_M + $t) {
          $punishment_money = C_PUNISHMENT2_V;
        } elseif($current>60*C_PUNISHMENT1_M+$t) {
          $punishment_money = C_PUNISHMENT1_V;
        }
        if($punishment_money!==0) {
          $userBillBLL->balance([
            'type' => 'income',
            'value' => $invitation['price'] - $punishment_money,
            'detail' => '取消邀请,返回(已按时间扣钱惩罚)'
          ], $buyer);
          $userBillBLL->balance([
            'type' => 'income',
            'value' => $punishment_money,
            'detail' => 'C取消订单,补偿给A'
          ], $seller);
        } else {
          $userBillBLL->balance([
            'type' => 'income',
            'value' => $invitation['price'],
            'detail' => '取消邀请'
          ], $buyer);
        }
        // C取消邀请订单, 发送给A
        $smsMesageBLL->sendMessage([
          'phone' => $seller['phone'],
          'type' => 'canceling2A',
          'cid' => $seller['cid'],
          'params' => [$seller['nickName'], $invitation['startAt']]
        ]);
        // C取消邀请订单, 发送给C
        $smsMesageBLL->sendMessage([
          'phone' => $buyer['phone'],
          'type' => 'canceling2C',
          'cid' => $buyer['cid'],
          'params' => [$buyer['nickName'], $invitation['startAt']]
        ]);
      } elseif($progress !== 'inviting') {
        thrower('invitation', 'updateFail', '只能取消邀请中和已接受状态的邀请!');
      } else {
        // inviting
        // C取消邀请订单, 发送给A
        $smsMesageBLL->sendMessage([
          'phone' => $seller['phone'],
          'type' => 'canceling2A',
          'cid' => $seller['cid'],
          'params' => [$seller['nickName'], $invitation['startAt']]
        ]);
        // C取消邀请订单, 发送给C
        $smsMesageBLL->sendMessage([
          'phone' => $buyer['phone'],
          'type' => 'canceling2C',
          'cid' => $buyer['cid'],
          'params' => [$buyer['nickName'], $invitation['startAt']]
        ]);
      }
    } elseif('canceled' === $status) {
      $input['status']='fail';
      $input['canceledAt'] = date('Y-m-d H:i:s');
      if($progress === 'accepted') {
        $user->balance([
          'type' => 'income',
          'value'=> $invitation['price'],
          'detail'=> 'canceled'
        ], $buyer);
        // A取消邀请订单, 发送给A
        $smsMesageBLL->sendMessage([
          'phone' => $seller['phone'],
          'type' => 'canceled2A',
          'cid' => $seller['cid'],
          'params' => [$seller['nickName'], $invitation['startAt']]
        ]);
        // A取消邀请订单, 发送给C
        $smsMesageBLL->sendMessage([
          'phone' => $buyer['phone'],
          'type' => 'canceled2C',
          'cid' => $buyer['cid'],
          'params' => [$buyer['nickName'], $invitation['startAt']]
        ]);
        // 按时间扣钱 -> 卖家不扣钱
      } else {
        thrower('invitation', 'updateFail', '只能取消已接受状态的邀请!');
      }
    } elseif('confirmed' === $status) {
      $input['status']='success';
      $input['confirmedAt'] = date('Y-m-d H:i:s');
      if($progress !== 'accepted') {
        thrower('invitation', 'updateFail', '接受邀请后才能进行确认!');
      }
      $input['confirmedAt'] = date('Y-m-d H:i:s');
      $smsMesageBLL->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'confirmed',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName'], $invitation['startAt']]
      ]);
    } else {
      throw new Exception($status.' 修改邀请进度错误!');
    }
    return model($this->table)->edit($invitatoinId, $input);
  }

  /**
   * 评论
   * 1.验证数据有效性
   * 2.邀请记录存在 
   * 3.isComment状态变化处理(yes就交易成功),其他字段处理
   * 4.如果是买家评论,则交易成功,分钱
   * @param {object} $user
   * @param {object} $input
   */
  function comment($user, $input) {
    $userBLL = new UserBLL();
    $userBillBLL = new UserBillBLL();
    $validation = new Validater([
      'id' => 'required|int',
      'type' => 'required|enum:buyer,seller',
      'comment' => 'required|string'
    ]);
    $data = $validation->validate($input);
    $invitation = self::getInfo($data['id']);
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    // 数据处理
    $type = $user['type'] === 'buyer' ? 'buyer' : 'seller';
    if($invitation['isComment']!=='not') {
      $data['isComment'] = 'yes';
      $data['status'] = 'success';
    } else {
      $data['isComment'] = $type === 'buyer' ? 'bought' : 'sold';
    }
    //$data['scoreOf'.$type] = $data['score'];
    $data['scoreOf'.$type] = 5;
    $data['commentOf'.$type] = $data['comment'];
    $data['commentOf'.$type.'At'] = date('Y-m-d H:i:s');
    unset($data['type']);
    unset($data['comment']);
    if($type === 'buyer') {
      $data['progress'] = 'success';
    }
    $invitation = $this->update($data, $invitation['id']);
    
    if($type === 'buyer') {
      $sellerAgency = $user->getInfo($invitation['sellerAgencyId']);
      $buyerAgency = $user->getInfo($invitation['buyerAgencyId']);
      // 中介返利
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $invitation['rebateAgency'],
        'detail' => 'seller-cashback'
      ], $sellerAgency);
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $invitation['rebateAgency'],
        'detail' => 'buyer-cashback'
      ], $buyerAgency);
      // 卖家进账
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $invitation['rebate'],
        'detail' => 'invitation'
      ], $user);
      // 平台收入
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $invitation['price']-$invitation['rebateAgency']*2-$invitation['rebate'],
        'detail' => 'platformIncome'
      ]);
    }
    return true;
  }

  /**
   * 投诉
   * 1.记录不存在/投诉过,不能进行投诉!
   */
  function complaint($invitationId, $type, $complaint) {
    $userBLL = new UserBLL();
    $smsMesageBLL = new SmsMessageBLL();
    $invitation = self::getInfo($invitationId);
    if(null === $invitation || $invitation['isComplaint'] == 1) {
      thrower('common', 'notFound');
    }
    // $sellerAgency = $userBLL->getInfo($invitation['sellerAgencyId']);
    // $buyerAgency = $userBLL->getInfo($invitation['buyerAgencyId']);
    // if($type === 'seller') {
    //   $smsMesageBLL->sendMessage([
    //     'phone' => $sellerAgency['phone'],
    //     'type' => 'complaintA2AB',
    //     'params' => [$sellerAgency['nickName'], $invitation['sellerName'], $invitation['startAt']]
    //   ]);
    //   $smsMesageBLL->sendMessage([
    //     'phone' => $buyerAgency['phone'],
    //     'type' => 'complaintA2CB',
    //     'params' => [$buyerAgency['nickName'], $invitation['buyerName'], $invitation['startAt']]
    //   ]);
    // } else {
    //   $smsMesageBLL->sendMessage([
    //     'phone' => $sellerAgency['phone'],
    //     'type' => 'complaintC2AB',
    //     'params' => [$buyerAgency['nickName'], $invitation['buyerName'], $invitation['startAt']]
    //   ]);
    //   $smsMesageBLL->sendMessage([
    //     'phone' => $buyerAgency['phone'],
    //     'type' => 'complaintC2CB',
    //     'params' => [$sellerAgency['nickName'], $invitation['sellerName'], $invitation['startAt']]
    //   ]);
    // }
    return self::update(['isComplaint'=> 1, 'complaint' => $complaint, 'complaintType'=>$type], $invitationId);
  }

  /**
   * 申请退款
   */
  function applyRefund($invitationId) {
    $invitation = self::getInfo($invitationId);
    if(null === $invitation || $invitation['isComplaint'] != 1) {
      thrower('common', 'notFound');
    }
    $invitation = self::update(['isRefund'=>'refunding','progress'=>'refund'], $invitationId);
    return $invitation;
  }
  /**
   * 接受退款
   */
  function acceptRefund($invitationId) {
    $invitation = self::getInfo($invitationId);
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    if($invitation['isComplaint']==1 && $invitation['isRefund'] == 'no') {
      $invitation = self::update(['progress'=>'refunding'], $invitationId);
    }
    return $invitation;
  }
  /**
   * (部分)退款
   */
  function refund($invitationId, $money) {
    $userBLL = new UserBLL();
    $userBillBLL = new UserBillBLL();
    $invitation = self::getInfo($invitationId);
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    if($invitation['isComplaint'] != 1) {
      thrower('invitation', 'refundFail', '这个邀请没有被投诉!');
    }
    if($invitation['price']<$money) {
      thrower('invitation', 'refundFail', '返现金额大于单价!');
    }
    self::update(['refund'=>$money, 'isRefund'=>'yes'], $invitationId);
    if($money!==0) {
      $buyer = $userBLL->getInfo($invitation['buyerId']);
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $money,
        'detail' => 'refund'
      ], $buyer);
      return true;
    }
  }
}

?>