<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserBLL extends BLL {

  public $table = 'user';
  
  static function auth($req) {
    $tokenData = $req->auth($req);
    $user = model('user')->getInfo(['id'=>$tokenData['uid']]);
    if(empty($user)) {
      thrower('user', 'userNotFound');
    }
    return $user;
  }

  function signUp($data) {
    $validation = new Validater([
      'type' => 'required|string|enum:buyer,servant,agency',
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18',
      'nickName' => 'required|string',
      'code' => 'required|string',
      'rccode' => 'required|string',
      'createdAt' => 'string|default:datetime'
    ]);
    $input = $validation->validate($data);
    $input['salt'] = _::random(24, 'mix');
    $rccode = $input['rccode'];
    $code = $input['code'];
    unset($input['rccode']);
    unset($input['code']);
    $result = model($this->table)->getInfo(['phone' => $input['phone']]);
    if(!empty($result)) {
      thrower('user', 'phoneRegistered');
    }
    // FIXME: 短信验证码,短信不够用,暂时注释
    SmsBLL::validateCode($input['phone'], $code);

    if($input['type'] !== 'agency') {
      if($rccode === null) {
        thrower('rccode', 'needRCcode');
      }
      $result = model('rccode')->getInfo(['rccode'=>$rccode]);
      if(empty($result)) {
        thrower('rccode', 'RCcodeNotFound');
      } else if($result['userId']!==null) {
        thrower('rccode', 'RCcodeUsed');
      } else if(time() > 60*10+strtotime($result['createdAt'])) {
        // 过期时间: 10封装
        thrower('rccode', 'RCcodeExpired');
      }
    }
    $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT, ['salt'=>$input['salt']]);

    $result = model($this->table)->add($input);
    model('rccode')->edit(['rccode'=>$rccode], ['userId'=>$result['id'], 'userName'=>$result['nickName'], 'userAvatar'=>$result['avatar'], 'type'=>$result['type']]);
    $result = model($this->table)->edit($result['id'], ['rccode'=>$rccode]);
    return $result;
  }

  function signIn($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18'
    ]);
    $input = $validation->validate($data);
    $result = model($this->table)->getInfo(['phone'=>$input['phone']]);
    $password = password_hash($input['password'], PASSWORD_BCRYPT, ['salt'=>$result['salt']]);
    if(empty($result)) {
      thrower('user', 'userNotFound');
    } else if($result['password']!==$password) {
      thrower('user', 'passwordError');
    }
    $token = $result['token'];
    $data = ['token'=>$token, 'type' => $result['type']];
    if($token!=='') {
      try {
        $token = (array)JWT::decode($data['token'], C_AUTH_KEY, array('HS256'));
      } catch(Exception $e) {
        $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'user'], C_AUTH_KEY);
        $data['token'] = $token;
        model($this->table)->edit(['phone'=>$input['phone']], $data);
      }
    } else {
      $token = JWT::encode(['exp'=>time()+C_AUTH_EXPIRED, 'uid'=>$result['id'], 'type'=>'user'], C_AUTH_KEY);
      $data['token'] = $token;
      model($this->table)->edit(['phone'=>$input['phone']], $data);
    }
    return $data;
  }

  /**
   * 忘记密码,短信重置
   */
  function forgotPassword($phone, $code, $newpsw) {
    $validation = new Validater([
      'phone' => 'required|string',
      'code' => 'required|string',
      'password' => 'required|string|alias:newpsw'
    ]);
    $data = $validation->validate($input);
    SmsBLL::validateCode($data['phone'], $data['code'], 'forgot');
    return $this->resetPassword($data['phone'], $data['newpsw']);
  }
  /**
   * 管理员重置密码
   */
  function resetPassword($phone, $newpsw) {
    $user = model($this->table)->getInfo(['phone'=>$phone]);
    if($user === null) {
      thrower('user', 'userNotFound');
    }
    $salt = _::random(24, 'mix');
    $newpsw = password_hash($newpsw, PASSWORD_BCRYPT, ['salt'=>$user['salt']]);
    model($this->table)->edit(['phone'=>$phone], ['password'=>$newpsw, 'salt'=>$salt, 'token'=>'']);
    return true;
  }
  /**
   * 用户修改密码
   */
  function changePassword($user, $input) {
    $validation = new Validater([
      'oldPassword' => 'required|string|alias:oldpsw',
      'newPassword' => 'required|string|alias:newpsw'
    ]);
    $data = $validation->validate($input);
    $salt = _::random(24, 'mix');
    $oldpsw = password_hash($data['oldpsw'], PASSWORD_BCRYPT, ['salt'=>$user['salt']]);
    $newpsw = password_hash($data['newpsw'], PASSWORD_BCRYPT, ['salt'=>$salt]);
    if($user['password']!==$oldpsw) {
      thrower('user', 'passwordError');
    }
    return $this->resetPassword($user['phone'], $newpsw);
  }

  function create($input) {
    $validation = new Validater([
      'phone' => 'required|string',
      'nickName' => 'required|string',
      'type' => 'required|enum:servant,buyer,agency',
      'password' => 'required|string|default:"123456"',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    $user = $this->getInfo(['phone'=>$data['phone']]);
    if($user!==null) {
      thrower('user', 'phoneRegistered');
    }
    $data['salt'] = _::random(24, 'mix');
    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['salt'=>$data['salt']]);
    return model($this->table)->add($data);
  }
  /**
   * TODO: 同步 rccode表的 name和avatar字段
   */
  function update($data, $condition) {
    $validation = new Validater([
      'identity' => 'string',
      'trueName' => 'string|maxlength:18',
      'nickName' => 'string|minlength:3|maxlength:18',
      'avatar' => 'string|maxlength:255',
      'introduce' => 'string|maxlength:255',
      'address' => 'string|maxlength:255',
      'city' => 'string|maxlength:255',
      'age' => 'nonzero|int|min:0|max:100',
      'money' => 'int',
      'height' => 'int',
      'weight' => 'int',
      'x' => 'float',
      'y' => 'float',
      'images' => 'int',
      'status' => 'string|enum:approving,approved,forbidden',
      'attr' => 'string|enum:normal,hot',
      'tags' => 'object|default:(toString)'
    ]);
    $input = $validation->validate($data);
    if(is_string($condition) || is_integer($condition)) {
      $condition = ['id'=>$condition];
    }
    $user = model($this->table)->edit($condition, $input);
    if($user['tags']!=='') {
      $user['tags'] = json_decode($user['tags']);
    }
    return $user;
  }

  function getList($hql) {
    $validation = new Validater([
      'type' => 'enum:servant,buyer,agency|ignore',
      'status' => 'enum:approved,approving,forbidden,registered|ignore',
      'attr' => 'enum:hot,recommend,normal|ignore',
      'search' => 'empty|string|default:""'
    ]);
    $hql['field'] = '!password,token,salt';
    $where = $validation->validate($hql['where']);
    if($where['search']!=='') {
      $where['phone|nickName'] = ['like', '%'.$where['search'].'%'];
    }
    unset($where['search']);
    $hql['where'] = $where;
    return model($this->table)->getList($hql);
  }

}
?>