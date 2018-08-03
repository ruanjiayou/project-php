<?php
use \Firebase\JWT\JWT;
use think\Request;

class TagBLL extends BLL {

  public $table = 'tag';
  
  public function create($data) {
    $validation = new Validater([
      'cataId' => 'required|int',
      'cataName' => 'required|string',
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->add($input);
  }

  public function update($data, $id) {
    $validation = new Validater([
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->edit(['id'=>$id], $input);
  }

}

?>