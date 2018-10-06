<?php
use \Firebase\JWT\JWT;
use think\Request;

class CatalogBLL extends BLL {

  public $table = 'catalog';
  
  public function create($data) {
    $validation = new Validater([
      'name' => 'required|string',
      'type' => 'required|enum:user,seller,buyer|default:"user"'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->add($input);
  }

  public function update($data, $id) {
    $validation = new Validater([
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    model('tag')->edit(['cataId'=>$id],['cataName'=>$input['name']]);
    return model($this->table)->edit(['id'=>$id], $input);
  }

}

?>