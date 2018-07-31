<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserBLL {
  
  static public function auth($req) {
    $tokenData = $req->auth($req);
    $user = model('user')->getInfo(['id'=>$tokenData['uid']]);
    if(empty($user)) {
      thrower('user', 'userNotFound');
    }
    return $user;
  }

  static public function signUp($data) {
    $validation = new Validater([
      'type' => 'required|string|enum:buyer,servant,agency',
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18',
      'nickName' => 'required|string',
      'code' => 'required|string',
      'rccode' => 'string|default:null',
      'createdAt' => 'string|default:date',
      'salt' => 'int|default:timestamps'
    ]);
    $input = $validation->validate($data);
    $rccode = $input['rccode'];
    $code = $input['code'];
    unset($input['rccode']);
    unset($input['code']);
    $result = model('user')->getInfo(['phone' => $input['phone']]);
    if(!empty($result)) {
      thrower('user', 'phoneRegistered');
    }
    if($input['type'] === 'servant') {
      if($rccode === null) {
        thrower('rccode', 'needRCcode');
      }
      $result = model('rccode')->getInfo(['rccode'=>$rccode]);
      if(empty($result)) {
        thrower('rccode', 'RCcodeNotFound');
      } else if($result['userId']!==null) {
        thrower('rccode', 'RCcodeUsed');
      }
    }
    //TODO: $code

    $result = model('user')->add($input);
    model('rccode')->edit(['rccode'=>$rccode], ['userId'=>$result['id']]);
    return $result;
  }

  static public function signIn($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18'
    ]);
    $input = $validation->validate($data);
    $result = model('user')->getInfo(['phone'=>$input['phone']]);
    if(empty($result)) {
      thrower('user', 'userNotFound');
    } else if($result['password']!==$input['password']) {
      thrower('user', 'passwordError');
    }
    $token = $result['token'];
    $data = ['token'=>$token];
    if($token!=='') {
      try {
        $token = (array)JWT::decode($data['token'], C_AUTH_KEY, array('HS256'));
      } catch(Exception $e) {
        $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'user'], C_AUTH_KEY);
        $data['token'] = $token;
        model('user')->edit(['phone'=>$input['phone']], $data);
      }
    } else {
      $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'user'], C_AUTH_KEY);
      $data['token'] = $token;
      model('user')->edit(['phone'=>$input['phone']], $data);
    }
    return $data;
  }

}
?>