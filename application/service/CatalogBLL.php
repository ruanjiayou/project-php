<?php
use \Firebase\JWT\JWT;
use think\Request;

class CatalogBLL extends BLL {

  public $table = 'catalog';
  
  public function create($data) {
    $validation = new Validater([
      'name' => 'required|string',
      'type' => 'required|enum:user,comment|default:"user"'
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