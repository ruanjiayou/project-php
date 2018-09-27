<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserBillBLL extends BLL {

  public $table = 'user_bill';

  function balance($input, $user=['id'=>0]) {
    $input['userId'] = $user['id'];
    $validation = new Validater([
      'userId' => 'required|int',
      'type' => 'required|enum:income,expent',
      'value' => 'required|int',
      'detail' => 'string',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $data = $validation->validate($input);
    // 平台收入不同步
    if($user['id']!==0) {
      $money = isset($data['type']) && $data['type'] === 'income' ? $user['money'] + $data['value'] : $user['money'] - $data['value'];
      (new UserBLL())->update(['money'=>$money], $user['id']);
    }
    return model($this->table)->add($data);
  }
}

?>