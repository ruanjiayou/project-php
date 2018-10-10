<?php
use \Firebase\JWT\JWT;
use think\Request;

class SmsMessageBLL extends BLL {
  
  public $table = 'sms_message';

  function create($input) {
    $validation = new Validater([
      'title' => 'empty|string',
      'content' => 'empty|string',
      'type' => 'required|enum:forgot,modify,zhuche,system,invite,cancel,refused,accepted,canceled|default:"system"',
      'phone' => 'empty|string|default:""',
      'code' => 'required|empty|string|default:""',
      'status' => 'required|string|default:"success"',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    return model($this->table)->add($data);
  }
  /**
   * 发送消息
   * 1.是system消息直接加到数据库,所有人能收到: 所需字段,title/content/type=system
   * 2.内部消息发送给指定人: 所需字段,phone/type/params
   * 3.短信消息则进行短信发送: 所需字段,phone/type/params
   */
  function sendMessage($input, $code = '', $hidden = false) {
    $validation = new Validater([
      'phone' => 'empty|string|default:""',
      'type' => 'required|string',
      'params' => 'required|array|default:array',
      'title' => 'empty|string|default:""',
      'content' => 'empty|string|default:""',
      'cid' => 'empty|string|default:""'
    ]);
    $data = $validation->validate($input);
    $one = model('user')->where(['phone'=>$data['phone']])->count();
    if(($data['type']==='forgot' || $data['type']==='modify') && $one===0) {
      thrower('user', 'phoneNotFound');
    }
    if($data['type'] === 'system') {
      $result = $this->create($data);
      return $result;
    }
    $place = (new SmsPlaceBLL())->getInfo(['place'=>$data['type']]);
    if($place === null) {
      thrower('sms', 'placeNotFound');
    }
    $content = $place['tpl'];
    for($i=0;$i<count($data['params']);$i++) {
      $content = _::replace($content, '{'.($i+1).'}', count($data['params']) > $i ? $data['params'][$i] : '');
    }
    if($place['isSms'] == 0) {
      $data['title'] = $place['sign'];
      $data['content'] = $content;
      $result = $this->create($data);
      if($data['cid']!="") {
        return (new GeTui())->sendOne(
          $data['phone'],
          $data['cid'],
          ['title'=>'['.$place['sign'].']', 'content'=> $content, 'payload'=> $content]
        );
      }
      return $result;
    } else if($place['signId'] == 0 || $place['tplId'] == 0) {
      thrower('sms', 'placeNotFound');
    } else {
      $message = $this->create([
        'type' => $place['place'],
        'title' => $place['sign'],
        'content' => $content,
        'code' => $code,
        'phone' => $data['phone'],
      ]);
      $result = $hidden === true ? ['result'=>0] : wxHelper::sendSmsMessage($data['phone'], $place['sign'], $place['tplId'], $data['params']);
      if($result['result']!==0) {
        $this->update(['status'=>'fail'], ['id'=>$message['id']]);
        thrower('sms', 'smsSendFail', $result['errmsg']);
      }
      $kv = [
        'zhuche' => '',
        'invite' => ''
      ];
      if($data['cid']!="" && isset($kv[$place['place']])) {
        (new GeTui())->sendOne(
          $data['phone'],
          $data['cid'],
          ['title'=>'['.$place['sign'].']', 'content'=> $content, 'payload'=> $content]
        );
      }
      return $message;
    }
  }

  function sendByProgress($invitation, $progress) {
    if(empty($invitation)) {
      return;
    }
    $startAt = substr($invitation['startAt'],5,-3);
    $userBLL = new UserBLL();
    $seller = $userBLL->getInfo($invitation['sellerId']);
    $buyer = $userBLL->getInfo($invitation['buyerId']);
    $sellerAgency = $userBLL->getInfo($invitation['sellerAgencyId']);
    $buyerAgency = $userBLL->getInfo($invitation['buyerAgencyId']);
    // 发送邀请消息 参数: A昵称
    if($progress === 'inviting') {
      $this->sendMessage([
        'phone' => $seller['phone'],
        'type' => 'invite',
        'cid' => $seller['cid'],
        'params' => [$seller['nickName']]
      ]);
    }
    // 拒绝发送消息 参数: C昵称
    if($progress === 'refused') {
      $this->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'refused',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName']]
      ]);
    }
    // 接受邀请消息
    if($progress == 'accepted') {
      // 接受邀请订单, 给A发送 参数: A昵称, 邀约时间
      $this->sendMessage([
        'phone' => $seller['phone'],
        'type' => 'accepted2A',
        'cid' => $seller['cid'],
        'params' => [$seller['nickName'], $startAt]
      ]);
      // 接受邀请订单, 给AB发送 AB昵称, 参数: A昵称, 邀约时间
      $this->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'accepted2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $seller['nickName'], $startAt]
      ]);
      // 接受邀请订单, 给C发送 参数: C昵称
      $this->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'accepted2C',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName']]
      ]);
      // 接受邀请订单, 给CB发送 参数: CB昵称, C昵称, 邀约时间
      $this->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'accepted2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $buyer['nickName'], $startAt]
      ]);
    }
    // 扫描发送消息
    if($progress == 'confirmed') {
      // 参数: A昵称, 邀约时间
      $this->sendMessage([
        'phone' => $seller['phone'],
        'type' => 'confirmed2A',
        'cid' => $seller['cid'],
        'params' => [$seller['nickName'], $startAt]
      ]);
      // 参数: C昵称, 邀约时间
      $this->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'confirmed2C',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName']]
      ]);
    }
    // A取消邀请(被动canceled)
    if($progress == 'canceled') {
      // A取消邀请订单, 发送给A 参数: A昵称, 邀约时间
      $this->sendMessage([
        'phone' => $seller['phone'],
        'type' => 'canceled2A',
        'cid' => $seller['cid'],
        'params' => [$seller['nickName'], $startAt]
      ]);
      // A取消邀请订单, 发送给C 参数: C昵称, 邀约时间
      $this->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'canceled2C',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName'], $startAt]
      ]);
      // 参数: AB昵称, A昵称, 邀约时间
      $this->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'canceled2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $seller['nickName'], $startAt]
      ]);
      // 参数: CB昵称, C昵称, 邀约时间
      $this->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'canceled2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $buyer['nickName'], $startAt]
      ]);
    }
    // C主动取消(canceling)
    if($progress == 'canceling') {
      $this->sendMessage([
        'phone' => $seller['phone'],
        'type' => 'canceling2A',
        'cid' => $seller['cid'],
        'params' => [$seller['nickName'], $startAt]
      ]);
      $this->sendMessage([
        'phone' => $buyer['phone'],
        'type' => 'canceling2C',
        'cid' => $buyer['cid'],
        'params' => [$buyer['nickName'], $startAt]
      ]);
      $this->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'canceling2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $seller['nickName'], $startAt]
      ]);
      $this->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'canceling2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $buyer['nickName'], $startAt]
      ]);
    }
    // A投诉
    if($progress == 'Acomplaint') {
      // 发送给A的上级
      $this->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'complaintA2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $seller['nickName'], $startAt]
      ]);
      // 发送给C的上级
      $this->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'complaintA2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $buyer['nickName'], $startAt]
      ]);
    }
    // C投诉
    if($progress == 'Ccomplaint') {
      // 发送给A的上级
      $this->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'complaintC2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $seller['nickName'], $startAt]
      ]);
      // 发送给C的上级
      $this->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'complaintC2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $buyer['nickName'], $startAt]
      ]);
    }
  }
  /**
   * 验证手机的验证码: zhuche/forgot/
   */
  static function validateCode($phone, $code, $type = 'zhuche') {
    $sms = (new SmsMessageBLL())->getInfo(['type'=>$type,'phone'=>$phone], ['order'=>'id DESC']);
    if($sms === null) {
      thrower('sms', 'codeError');
    } else {
      if($sms['code']!==$code || $sms['code'] === '') {
        thrower('sms', 'codeError');
      }
      if(time() > 60*10+strtotime($sms['createdAt'])) {
        thrower('sms', 'codeExpired');
      }
    }
  }
}
?>