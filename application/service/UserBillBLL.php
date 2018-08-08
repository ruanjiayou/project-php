<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserBillBLL extends BLL {

  public $table = 'user_bill';
  
  public function create($data) {
    $validation = new Validater([
      'userId' => 'required|int',
      'type' => 'required|enum:income,expent',
      'value' => 'required|int|nonzero',
      'detail' => 'string',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->add($input);
  }

}

?>