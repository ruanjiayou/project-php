<?php
use \Firebase\JWT\JWT;
use think\Request;

class TagBLL extends BLL {

  public $table = 'tag';
  
  public function create($data) {
    $validation = new Validater([
      'cataId' => 'required|int',
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    $catalog = (new CatalogBLL())->getInfo($input['cataId']);
    $input['cataName'] = $catalog['name'];
    $input['type'] = $catalog['type'];
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