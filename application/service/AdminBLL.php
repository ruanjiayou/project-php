<?php
use \Firebase\JWT\JWT;
use think\Request;

class AdminBLL {
  
  static public function auth($req) {
    $tokenData = $req->auth($req);
    $admin = model('admin')->getInfo(['id'=>$tokenData['uid']]);
    if(empty($admin)) {
      thrower('user', 'userNotFound');
    }
    return $admin;
  }

  static public function signIn($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18'
    ]);
    $input = $validation->validate($data);
    $result = model('admin')->getInfo(['phone'=>$input['phone']]);
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
        $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'admin'], C_AUTH_KEY);
        $data['token'] = $token;
        model('admin')->edit(['phone'=>$input['phone']], $data);
      }
    } else {
      $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'admin'], C_AUTH_KEY);
      $data['token'] = $token;
      model('admin')->edit(['phone'=>$input['phone']], $data);
    }
    return $data;
  }

  static public function create($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'nickName' => 'required|string',
      'password' => 'string|default:"123456"',
      'salt' => 'string|default:timestamps',
      'isSA' => 'boolean|default:false',
      'createdAt' => 'string|default:date'
    ]);
    $input = $validation->validate($data);
    $admin = model('admin')->getInfo(['phone'=>$input['phone']]);
    if(!empty($admin)) {
      thrower('user', 'phoneRegistered');
    }
    $admin = model('admin')->add($input);
    return $admin;
  }

  static public function destroy($condition) {
    return model('admin')->remove($condition);
  }

  static public function update($data, $condition) {
    $validation = new Validater([
      'nickName' => 'string|minlength:3|maxlength:18',
      'avatar' => 'string|maxlength:255'
    ]);
    $input = $validation->validate($data);
    $admin = model('admin')->edit($condition, $data);
    return $admin;
  }

  static public function changeRight($adminId, $data) {
    $validation = new Validater([
      'rights' => 'required|array'
    ]);
    $rights = $validation->validate($data);
    model('admin_auth')->remove(['adminId'=>$adminId]);
    foreach($rights['rights'] as $r) {
      model('admin_auth')->add(['adminId'=>$adminId,'authorityId'=>$r]);
    }
    return true;
  }

}
?>