<?php
use \Firebase\JWT\JWT;
use think\Request;

class InvitationBLL extends BLL {

  public $table = 'invitation';
  
  public function invite($user, $input) {
    if($user['type']!=='buyer') {
      throw new Error('本用户类型没有此项权限!');
    }
    //TODO: 价格从表中取
    $validation = new Validater([
      'userId' => 'required|int|alias:sellerId',
      'price' => 'required|int',
      'x' => 'required|float',
      'y' => 'required|float',
      'address' => 'required|string',
      'startAt' => 'required|dateonly',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    if(false === _::isBefore(date('Y-m-d H:i:s'), $data['startAt'])) {
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
    return model($this->table)->add($data);
  }

  public function changeProgress($invitatoinId, $status, $user) {
    if($user['type'] !== 'buyer' && $status === 'canceling' || $user['type']!=='servant' && in_array($status, ['refused','canceled','accepted','comfirmed'])) {
      throw new Exception('本用户类型没有此项权限!');
    }
    $invitation = self::getInfo($invitatoinId);
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
        $buyer = (new UserBLL())->getInfo($invitation['buyerId']);
        if(null === $buyer) {
          thrower('user', 'userNotFound');
        }
        if($buyer['money'] < $invitation['price']) {
          thrower('user', 'moneyLess');
        } else {
          (new UserBillBLL())->balance([
            'type' => 'expent',
            'value' => $invitation['price'],
            'detail' => '接受邀请,扣钱'
          ], $buyer);
        }
      }
    } elseif('canceling' === $status) {
      if($progress === 'accepted') {
        // TODO: 按时间扣钱
      } elseif($progress !== 'inviting') {
        thrower('invitation', 'updateFail', '只能取消邀请中和已接受状态的邀请!');
      }
    } elseif('canceled' === $status) {
      if($progress === 'accepted') {
        // TODO: 按时间扣钱
      } else {
        thrower('invitation', 'updateFail', '只能取消已接受状态的邀请!');
      }
    } elseif('confirmed' === $status) {
      if($progress === 'accepted') {
        $input['status'] = 'success';
      } else {
        thrower('invitation', 'updateFail', '接受邀请后才能进行确认!');
      }
    } else {
      throw new Exception($status.' 修改邀请进度错误!');
    }
    return model($this->table)->edit($invitatoinId, $input);
  }

  function comment($invitationId) {
    $validation = new Validater([
      'userId' => 'required|int',
      'comment' => 'required|string',
      'type' => 'required|enum:1,10',
      'score' => 'required|int'
    ]);
  }

  function sellerComment() {

  }
}

?>