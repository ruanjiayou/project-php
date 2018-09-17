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
    if($user['status'] === 'forbidden') {
      thrower('user', 'forbidden');
    }
    return $user;
  }

  /**
   * 注册
   * 1.验证字段
   * 2.验证手机号是否注册
   * 3.验证短信验证码
   * 4.验证推荐码
   * 5.生成随机盐
   * 6.记录推荐关系
   */
  function signUp($data) {
    $validation = new Validater([
      'type' => 'required|string|enum:buyer,servant,agency',
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18',
      'nickName' => 'required|string',
      'code' => 'required|string',
      'rccode' => 'required|string',
      'createdAt' => 'string|default:datetime',
      'rebate' => 'int|default:0'
    ]);
    $input = $validation->validate($data);
    if($input['type'] === 'servant') {
      $input['rebate'] = 60;
    }
    if($input['type'] === 'buyer') {
      $input['status'] = 'approved';
    }
    $input['salt'] = _::random(24, 'mix');
    $rccode = $input['rccode'];
    $code = $input['code'];
    unset($input['rccode']);
    unset($input['code']);
    $result = model($this->table)->getInfo(['phone' => $input['phone']]);
    if(!empty($result)) {
      thrower('user', 'phoneRegistered');
    }
    // 短信验证码,短信不够用,可以注释下面一行
    SmsMessageBLL::validateCode($input['phone'], $code);

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
    if($input['type']==='servant') {
      $priceBLL = new PriceBLL();
      $priceBLL->create(['type'=>'order','userId'=>$result['id'],'value'=>100]);
      $priceBLL->create(['type'=>'order','userId'=>$result['id'],'value'=>200]);
      $priceBLL->create(['type'=>'order','userId'=>$result['id'],'value'=>300]);
    }
    model('rccode')->edit(['rccode'=>$rccode], ['userId'=>$result['id'], 'userName'=>$result['nickName'], 'userAvatar'=>$result['avatar'], 'userPhone' => $result['phone'], 'type'=>$result['type']]);
    $result = model($this->table)->edit($result['id'], ['rccode'=>$rccode]);
    return $result;
  }

  /**
   * 登录返回鉴权token和角色类型type
   */
  function signIn($data) {
    $validation = new Validater([
      'phone' => 'required|string|minlength:7|maxlength:11',
      'password' => 'required|string|minlength:6|maxlength:18'
    ]);
    $input = $validation->validate($data);
    $result = model($this->table)->getInfo(['phone'=>$input['phone']]);
    if(empty($result)) {
      thrower('user', 'userNotFound');
    }
    $password = password_hash($input['password'], PASSWORD_BCRYPT, ['salt'=>$result['salt']]);
    if($result['password']!==$password) {
      thrower('user', 'passwordError');
    }
    $token = $result['token'];
    $data = ['token'=>$token, 'type' => $result['type'], 'status' => $result['status']];
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
  function forgotPassword($input) {
    $validation = new Validater([
      'phone' => 'required|string',
      'code' => 'required|string',
      'password' => 'required|string|alias:newpsw'
    ]);
    $data = $validation->validate($input);
    $user = model($this->table)->getInfo(['phone'=>$data['phone']]);
    if($user === null) {
      thrower('user', 'userNotFound');
    }
    SmsMessageBLL::validateCode($data['phone'], $data['code'], 'forgot');
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
    $newpsw = password_hash($newpsw, PASSWORD_BCRYPT, ['salt'=>$salt]);
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
      'status' => 'required|enum:registered,approving,approved,forbidden|default:"registered"',
      'createdAt' => 'required|date|default:datetime',
      'rebate' => 'int|default:0'
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
   * 业务逻辑: 同步 rccode表的 name和avatar字段
   */
  function update($data, $condition) {
    $validation = new Validater([
      'identity' => 'string',
      'trueName' => 'string|maxlength:18',
      'alipay' => 'string',
      'creditCard' => 'string',
      'nickName' => 'string|minlength:3|maxlength:18',
      'avatar' => 'string|maxlength:255',
      'introduce' => 'string|maxlength:255',
      'address' => 'string|maxlength:255',
      'city' => 'string|maxlength:255',
      'cityId' => 'int|nonzero',
      'age' => 'nonzero|int|min:0|max:100',
      'money' => 'int',
      'height' => 'int',
      'weight' => 'int',
      'popular' => 'int',
      'x' => 'float:10,6',
      'y' => 'float:10,6',
      'images' => 'int',
      'status' => 'string|enum:approving,approved,forbidden',
      'attr' => 'string|enum:normal,hot,recommend',
      'tags' => 'object|default:(toString)',
      'rebate' => 'int|min:60|max:75',
      'cid' => 'string',
      'cidtoken' => 'string',
      'willless' => 'boolean'
    ]);
    $input = $validation->validate($data);
    if(isset($input['cid'])) {
      model($this->table)->edit(['cid'=>$input['cid']], ['cid'=>'','cidtoken'=>'']);
    }
    if(is_string($condition) || is_integer($condition)) {
      $condition = ['id'=>$condition];
    }
    $user = $this->getInfo($condition);
    if($user===null) {
      thrower('user', 'userNotFound');
    }
    $type = $user['type'];
    // 只能设置秘书的佣金比例 60-75之间
    if($type !== 'servant') {
      unset($input['rebate']);
    }
    $user = model($this->table)->edit($condition, $input);
    if($user['tags']!=='') {
      $user['tags'] = json_decode($user['tags']);
    }
    if($type === 'buyer') {
      if(isset($input['nickName']) || isset($input['avatar'])) {
        // 同步invitation表买家 name/avatar
        model('invitation')->edit(['buyerId'=>$user['id']], [
          'buyerName' => isset($input['nickName']) ? $input['nickName'] : $user['nickName'],
          'buyerAvatar' => isset($input['avatar']) ? $input['avatar'] : $user['avatar']
        ]);
        // 同步rccode表 buyer用户 name/avatar
        model('rccode')->edit(['userId'=>$user['id']], [
          'userName' => isset($input['nickName']) ? $input['nickName'] : $user['nickName'],
          'userAvatar' => isset($input['avatar']) ? $input['avatar'] : $user['avatar']
        ]);
      }
    }
    if($type === 'servant') {
      if(isset($input['nickName']) || isset($input['avatar'])) {
        // 同步invitation表买家 name/avatar
        model('invitation')->edit(['sellerId'=>$user['id']], [
          'sellerName' => isset($input['nickName']) ? $input['nickName'] : $user['nickName'],
          'sellerAvatar' => isset($input['avatar']) ? $input['avatar'] : $user['avatar']
        ]);
        // 同步rccode表 seller用户 name/avatar
        model('rccode')->edit(['userId'=>$user['id']], [
          'userName' => isset($input['nickName']) ? $input['nickName'] : $user['nickName'],
          'userAvatar' => isset($input['avatar']) ? $input['avatar'] : $user['avatar']
        ]);
      }
    }
    if($type === 'agency') {
      if(isset($input['nickName']) || isset($input['avatar'])) {
        // 同步rccode表 agency用户 name/avatar
        model('rccode')->edit(['userId'=>$user['id']], [
          'agencyName' => isset($input['nickName']) ? $input['nickName'] : $user['nickName'],
          'agencyAvatar' => isset($input['avatar']) ? $input['avatar'] : $user['avatar']
        ]);
      }
    }
    return $user;
  }

  function getList($hql) {
    $validation = new Validater([
      'type' => 'enum:servant,buyer,agency|ignore',
      'status' => 'enum:approved,approving,forbidden,registered|ignore',
      'attr' => 'enum:hot,recommend,normal|ignore',
      'cityId' => 'int'
    ]);
    $hql['field'] = '!password,token,salt';
    $where = $validation->validate($hql['where']);
    if($hql['search']!=='') {
      $where['phone|nickName'] = ['like', '%'.$hql['search'].'%'];
    }
    unset($hql['search']);
    $hql['where'] = $where;
    return model($this->table)->getList($hql);
  }

  function getInfo($condition, $opts=[]) {
    $result = model($this->table)->getInfo($condition, $opts);
    if($this->strict === true && null === $result) {
      thrower('common', 'notFound');
    }
    if(null !== $result) {
      $result['pictures'] = (new UserImageBLL())->getAll(['where'=>['userId'=>$result['id']],'field'=>'id,url']);
      $result['prices'] = (new PriceBLL())->getAll(['where'=>['userId'=>$result['id']], 'field'=>'id,value','order'=> 'value DESC']);
      if(isset($result['tags'])) {
        $result['tags'] = json_decode($result['tags']);
      }
    }
    return $result;
  }
}
?>