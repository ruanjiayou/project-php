<?php
use \Firebase\JWT\JWT;
use think\Request;

class AdminBLL extends BLL {
  
  public $table = 'admin';

  static public function auth($req) {
    $tokenData = $req->auth($req);
    $admin = model('admin')->getInfo(['id'=>$tokenData['uid']]);
    if(empty($admin)) {
      thrower('user', 'userNotFound');
    }
    return $admin;
  }

  function resetPassword($phone, $newpsw) {
    $admin = model($this->table)->getInfo(['phone'=>$phone]);
    if($admin === null) {
      thrower('user', 'userNotFound');
    }
    $salt = _::random(24, 'mix');
    $newpsw = password_hash($newpsw, PASSWORD_BCRYPT, ['salt'=>$salt]);
    model($this->table)->edit(['phone'=>$phone], ['password'=>$newpsw, 'salt'=>$salt, 'token'=>'']);
    return true;
  }

  function changePassword($admin, $oldpsw, $newpsw) {
    $oldpsw = password_hash($oldpsw, PASSWORD_BCRYPT, ['salt'=>$admin['salt']]);
    if($admin['password']!==$oldpsw) {
      thrower('user', 'passwordError');
    }
    return $this->resetPassword($admin['phone'], $newpsw);
  }

  function signIn($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18'
    ]);
    $input = $validation->validate($data);
    $result = $this->getInfo(['phone'=>$input['phone']],['scopes'=>['AdminAuth']]);
    if(empty($result)) {
      thrower('user', 'userNotFound');
    }
    $password = password_hash($input['password'], PASSWORD_BCRYPT, ['salt'=>$result['salt']]);
    if($result['password']!==$password) {
      thrower('user', 'passwordError');
    }
    $token = $result['token'];
    $data = ['token'=>$token,'AdminAuth'=>isset($result['AdminAuth']) ? $result['AdminAuth'] : []];
    if($token!=='') {
      try {
        $token = (array)JWT::decode($data['token'], C_AUTH_KEY, array('HS256'));
      } catch(Exception $e) {
        $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'admin'], C_AUTH_KEY);
        $data['token'] = $token;
        model($this->table)->edit(['phone'=>$input['phone']], ['token'=>$data['token']]);
      }
    } else {
      $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'admin'], C_AUTH_KEY);
      $data['token'] = $token;
      model($this->table)->edit(['phone'=>$input['phone']], ['token'=>$data['token']]);
    }
    $data['isSA'] = $result['isSA'];
    return $data;
  }

  function create($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'nickName' => 'required|string',
      'password' => 'string|default:"123456"',
      'isSA' => 'boolean|default:false',
      'createdAt' => 'string|default:datetime'
    ]);
    $input = $validation->validate($data);
    $input['salt'] = _::random(24, 'mix');
    $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT, ['salt'=>$input['salt']]);
    $admin = model($this->table)->getInfo(['phone'=>$input['phone']]);
    if(!empty($admin)) {
      thrower('user', 'phoneRegistered');
    }
    $admin = model($this->table)->add($input);
    return $admin;
  }

  function update($data, $condition) {
    $validation = new Validater([
      'nickName' => 'string|minlength:3|maxlength:18',
      'avatar' => 'string|maxlength:255'
    ]);
    $input = $validation->validate($data);
    if(is_string($condition) || is_integer($condition)) {
      $condition = ['id'=>$condition];
    }
    $admin = model($this->table)->edit($condition, $data);
    return $admin;
  }

  function changeRight($adminId, $data) {
    $validation = new Validater([
      'rights' => 'required|array'
    ]);
    $rights = $validation->validate($data);
    $rights = model('authority')->getList(['limit'=>0,'where'=>['id'=>['in',$rights['rights']]]]);
    model('admin_auth')->remove(['adminId'=>$adminId]);
    foreach($rights as $r) {
      model('admin_auth')->add(['adminId'=>$adminId,'authorityId'=>$r['id'], 'authorityName'=>$r['name']]);
    }
    return true;
  }

}
?>