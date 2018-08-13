<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserBillBLL extends BLL {

  public $table = 'user_bill';

  function balance($input, $user) {
    $input['userId'] = $user['id'];
    $validation = new Validater([
      'userId' => 'required|int',
      'type' => 'required|enum:income,expent',
      'value' => 'required|int|nonzero',
      'detail' => 'string',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $data = $validation->validate($input);
    $money = $data['type'] === 'income' ? $user['money'] + $data['value'] : $user['money'] - $data['value'];
    (new UserBLL())->update(['money'=>$money], $user['id']);
    return model($this->table)->add($data);
  }
}

?>