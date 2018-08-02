<?php
use \Firebase\JWT\JWT;
use think\Request;

class CatalogBLL {
  
  static public function create($data) {
    $validation = new Validater([
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model('catalog')->add($input);
  }

  static public function destroy($data) {
    $validation = new Validater([
      'id' => 'required|array'
    ]);
    $input = $validation->validate($data);
    return model('catalog')->remove(['id'=>['in', $input['id']]]);
  }

  static public function update($data, $id) {
    $validation = new Validater([
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model('catalog')->edit(['id'=>$id], $input);
  }

  static public function getList() {
    return model('catalog')->getList(['limit'=>0]);
  }

  static public function getInfo($id) {
    return model('catalog')->getInfo(['id'=>$id]);
  }

}

?>