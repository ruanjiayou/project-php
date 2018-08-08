<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserImageBLL extends BLL {

  public $table = 'user_image';
  
  public function create($data) {
    $validation = new Validater([
      'userId' => 'required|int',
      'url' => 'required|string',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->add($input);
  }

  public function update($data, $id) {
    $validation = new Validater([
      'url' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->edit(['id'=>$id], $input);
  }

}

?>