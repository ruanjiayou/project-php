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
          ['title'=>'[商务之星]', 'content'=> $content, 'payload'=> $content]
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
        'zhuche' => ''
      ];
      if($data['cid']!="" && isset($kv[$place['place']])) {
        return (new GeTui())->sendOne(
          $data['phone'],
          $data['cid'],
          ['title'=>'['.$place['sign'].']', 'content'=> $content, 'payload'=> $content]
        );
      }
      return $message;
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