<?php
use \Firebase\JWT\JWT;
use think\Request;

class SmsMessageBLL extends BLL {
  
  public $table = 'sms_message';

  function create($input) {
    $validation = new Validater([
      'title' => 'required|string',
      'content' => 'required|string',
      'type' => 'required|string|default:"system"',
      'phone' => 'string|default:""',
      'code' => 'required|string|default:""',
      'status' => 'required|string|default:"success"',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    return model($this->table)->add($data);
  }
  /**
   * 根据手机号和预设类型发送短信和参数
   */
  function sendMessage($input, $code = '') {
    $validation = new Validater([
      'phone' => 'required|string',
      'type' => 'required|enum:forgot,modify,zhuche,system,invite,cancel,refused,accepted,canceled',
      'params' => 'required|array'
    ]);
    $data = $validation->validate($input);
    $place = model('sms_place')->getInfo(['place'=>$data['type']]);
    if($place === null || $place['signId'] === 0 || $place['tplId'] === 0) {
      thrower('sms', 'tplNotFound');
    }
    $content = $place['tpl'];
    for($i=0;$i<count($data['params']);$i++) {
      $content = _::replace($content, '{'.($i+1).'}', $data['params'][$i]);
    }
    $message = $this->create([
      'type' => $place['place'],
      'title' => $place['sign'],
      'content' => $content,
      'code' => $code,
      'phone' => $data['phone'],
    ]);
    $result = wxHelper::sendSmsMessage($data['phone'], $place['sign'], $place['tplId'], $data['params']);
    if($result['result']!==0) {
      $this->update(['status'=>'fail'], ['id'=>$message['id']]);
      thrower('sms', 'smsSendFail', $result['errmsg']);
    }
    return $result;
  }

  /**
   * 验证手机的验证码
   */
  static function validateCode($phone, $code, $type = 'zhuche') {
    $sms = model('sms_message')->getInfo(['type'=>$type,'phone'=>$phone], ['order'=>'id DESC']);
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