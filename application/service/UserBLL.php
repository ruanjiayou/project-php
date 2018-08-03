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
      'rccode' => 'string|default:null',
      'createdAt' => 'string|default:date',
      'salt' => 'int|default:timestamps'
    ]);
    $input = $validation->validate($data);
    $rccode = $input['rccode'];
    $code = $input['code'];
    unset($input['rccode']);
    unset($input['code']);
    $result = model($this->table)->getInfo(['phone' => $input['phone']]);
    if(!empty($result)) {
      thrower('user', 'phoneRegistered');
    }
    if($input['type'] !== 'agency') {
      if($rccode === null) {
        thrower('rccode', 'needRCcode');
      }
      $result = model($this->table)->getInfo(['rccode'=>$rccode]);
      if(empty($result)) {
        thrower('rccode', 'RCcodeNotFound');
      } else if($result['userId']!==null) {
        thrower('rccode', 'RCcodeUsed');
      } else if(time() > 60*10+strtotime($result['createdAt'])) {
        // 过期时间: 10封装
        thrower('rccode', 'RCcodeExpired');
      }
    }
    //TODO: 短信验证码$code

    $result = model($this->table)->add($input);
    model('rccode')->edit(['rccode'=>$rccode], ['userId'=>$result['id'], 'userName'=>$result['nickName'], 'userAvatar'=>$result['avatar'], 'type'=>$result['type']]);
    return $result;
  }

  function signIn($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18'
    ]);
    $input = $validation->validate($data);
    $result = model($this->table)->getInfo(['phone'=>$input['phone']]);
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
      'type' => 'enum:servant,buyer,agency',
      'status' => 'enum:approved,approving,forbidden,registered',
      'attr' => 'enum:hot,recommend,normal',
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