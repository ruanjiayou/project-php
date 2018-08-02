<?php
use \Firebase\JWT\JWT;
use think\Request;

class TagBLL {
  
  static public function create($data) {
    $validation = new Validater([
      'cataId' => 'required|int',
      'cataName' => 'required|string',
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model('tag')->add($input);
  }

  static public function destroy($data) {
    $validation = new Validater([
      'id' => 'required|array'
    ]);
    $input = $validation->validate($data);
    return model('tag')->remove(['id'=>['in', $input['id']]]);
  }

  static public function update($data, $id) {
    $validation = new Validater([
      'name' => 'required|string'
    ]);
    $input = $validation->validate($data);
    return model('tag')->edit(['id'=>$id], $input);
  }

  static public function getList() {
    return model('tag')->getList(['limit'=>0]);
  }

  static public function getInfo($id) {
    return model('tag')->getInfo(['id'=>$id]);
  }

}

?>