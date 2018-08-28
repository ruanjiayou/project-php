<?php
use \Firebase\JWT\JWT;
use think\Request;

class InvitationBLL extends BLL {

  public $table = 'invitation';
  
  /**
   * 发出邀请
   * 1.查询上级和价格数据
   * 2.邀请时间验证
   * 3.工作状态验证
   * 4.用户状态验证
   * 5.发送消息
   */
  function invite($user, $input) {
    if($user['type']!=='buyer') {
      throw new Error('本用户类型没有此项权限!');
    }
    $validation = new Validater([
      'userId' => 'required|int|alias:sellerId',
      'priceId' => 'required|int|alias:price',
      'x' => 'required|float',
      'y' => 'required|float',
      'address' => 'required|string',
      'startAt' => 'required|dateonly',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    $sellerrccode = (new RccodeBLL())->getInfo(['userId'=>$data['userId']]);
    $buyerrccode = (new RccodeBLL())->getInfo(['userId'=>$user['id']]);
    $data['sellerAgencyId'] = $sellerrccode['agencyId'];
    $data['buyerAgencyId'] = $buyerrccode['agencyId'];
    $price = (new PriceBLL())->getInfo($data['price']);
    $data['price'] = $price['value'];
    if(false === _::isBefore(date('Y-m-d'), $data['startAt'])) {
      thrower('invitation', 'dateInvalid');
    }
    $isWork = (new UserWorkBLL())->isWork(['userId'=>$data['sellerId'], 'workAt'=>$data['startAt']]);
    if(false === $isWork) {
      thrower('invitation', 'userDontWork');
    }
    if($data['price'] > $user['money']) {
      thrower('user', 'moneyLess');
    }
    $seller = (new UserBLL())->getInfo($data['sellerId']);
    if(null === $seller || $seller['status']!=='approved') {
      thrower('user', 'userNotFound');
    }
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
   * 3.refused/accepted/canceling/canceled/comfirmed
   * 4.取消邀请,超过指定时间则扣钱
   */
  public function changeProgress($invitatoinId, $status, $user) {
    $userBillBLL = new UserBillBLL();
    if($user['type'] !== 'buyer' && $status === 'canceling' || $user['type']!=='servant' && in_array($status, ['refused','canceled','accepted','comfirmed'])) {
      throw new Exception('本用户类型没有此项权限!');
    }
    $invitation = self::getInfo($invitatoinId);
    $buyer = null;
    $seller = null;
    if($user['type'] === 'buyer') {
      $buyer = $user;
      $selller = (new UserBLL())->getInfo($invitation['sellerId']);
    } else {
      $seller = $user;
      $buyer = (new UserBLL())->getInfo($invitation['buyerId']);
    }
    // accepted状态保持pending,confirmed改为success,其他都是fail
    $input = ['progress'=>$status, 'status'=>'fail'];
    $progress = $invitation['progress'];
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    // pending: inviting accepted
    // success: comfirmed
    // fail: refused canceling canceled expired 
    if('refused' === $status) {
      if($progress !== 'inviting') {
        thrower('invitation', 'updateFail', '只能取消邀请中状态的邀请!');
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
    } elseif('canceling' === $status) {
      if($progress === 'accepted') {
        $userBillBLL->balance([
          'type' => 'income',
          'value' => $invitation['price'],
          'detail' => '取消邀请'
        ], $buyer);
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
            'type' => 'expent',
            'value' => $punishment_money,
            'detail' => '取消邀请惩罚'
          ], $buyer);
        }
      } elseif($progress !== 'inviting') {
        thrower('invitation', 'updateFail', '只能取消邀请中和已接受状态的邀请!');
      }
    } elseif('canceled' === $status) {
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
            'type' => 'expent',
            'value' => $punishment_money,
            'detail' => '取消邀请惩罚'
          ], $seller);
        }
        (new SmsMessageBLL())->sendMessage([
          'phone' => $buyer['phone'],
          'type' => 'canceled',
          'params' => [$seller['nickName']]
        ]);
      } else {
        thrower('invitation', 'updateFail', '只能取消已接受状态的邀请!');
      }
    } elseif('confirmed' === $status) {
      if($progress !== 'accepted') {
        thrower('invitation', 'updateFail', '接受邀请后才能进行确认!');
      }
    } else {
      throw new Exception($status.' 修改邀请进度错误!');
    }
    if(in_array($status, ['canceled','canceling','accepted','refused'])) {
      if($status ==='canceling') {
        (new SmsMessageBLL())->sendMessage([
          'phone' => $seller['phone'],
          'type' => $status,
          'params' => [$buyer['nickName']]
        ]);
      } else {
        (new SmsMessageBLL())->sendMessage([
          'phone' => $buyer['phone'],
          'type' => $status,
          'params' => [$seller['nickName']]
        ]);
      }
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
      'userId' => 'required|int',
      'type' => 'required|enum:buyer,seller',
      'score' => 'required|int',
      'comment' => 'required|string'
    ]);
    $data = $validation->validate($input);
    $invitation = self::getInfo($data['id']);
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    // 数据处理
    $type = $user['type'];
    if($invitation['isComment']!=='not') {
      $data['isComment'] = 'yes';
      $data['status'] = 'success';
    } else {
      $data['isComment'] = $type === 'buyer' ? 'bought' : 'sold';
    }
    $data['scoreOf'+$data['type']] = $data['score'];
    $data['commentOf'+$data['type']] = $data['comment'];
    unset($data['type']);
    unset($data['score']);
    unset($data['comment']);
    $invitation = self::edit($data['id'], $data);
    
    if($type === 'buyer') {
      $rebate = $PriceBLL::getRebate();
      $agency = $userBLL->getInfo($invitation['agencyId']);
      $seller = $userBLL->getInfo($invitation['sellerId']);
      // 分钱规则
      $sellerPrice = round($rebate['value']/100*$invitation['price']);
      $platfmPrice = $invitation['price']*C_MONEY_PLATFOM;
      $agencyPrice = $invitation['price'] - $sellerPrice - $platfmPrice;
      // 中介返利
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $agencyPrice,
        'detail' => 'cashback'
      ], $agency);
      // 卖家进账
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $sellerPrice,
        'detail' => 'invitation'
      ], $user);
      // 平台收入
      $userBillBLL->balance([
        'type' => 'income',
        'value' => $platfmPrice
      ]);
    }
    return true;
  }

  /**
   * 投诉
   * 1.记录存在.投诉过或评论过.不能进行投诉!
   * 2.金额判断
   */
  function complaint($invitationId, $complaint) {
    $invitation = self::getInfo($invitationId);
    if(null === $invitation) {
      thrower('common', 'notFound');
    }
    if($invitation['isComplaint'] === 1 || $invitation['isComment'] === 'yes' || $invitation['isComment']==='bought') {
      thrower('invitation', 'cantComplaint');
    }
    return self::update(['isComplaint'=>true, 'complaint'=>$complaint], $invitationId);
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
    if($invitation['isComplaint'] !== 1) {
      thrower('invitation', 'refundFail', '这个邀请没有被投诉!');
    }
    if($invitation['price']<$money) {
      thrower('invitation', 'refundFail', '返现金额大于单价!');
    }
    self::update(['isRefund'=>true, 'refund'=>$money], $invitationId);

    $buyer = $userBLL->getInfo($invitation['buyerId']);
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $money,
      'detail' => 'refund'
    ], $buyer);
    return true;
  }
}

?>