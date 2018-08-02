<?php
use \Firebase\JWT\JWT;
use think\Request;

class BLL {
  
  static public $table = '';

  static public function create($data) {
    // $validation = new Validater([
    //   'name' => 'required|string'
    // ]);
    // $input = $validation->validate($data);
    // return model('tag')->add($input);
    return model(self::$table)->add($data);
  }

  static public function destroy($data) {
    $validation = new Validater([
      'id' => 'required|array'
    ]);
    $input = $validation->validate($data);
    return model(self::$table)->remove(['id'=>['in', $input['id']]]);
  }

  static public function update($data, $condition) {
    // $validation = new Validater([
    //   'name' => 'required|string'
    // ]);
    // $data = $validation->validate($data);
    if(_::isInteger($condition)) {
      $condition = ['id'=>$condition];
    }
    return model(self::$table)->edit($condition, $data);
  }

  static public function getAll($hql) {
    $hql['limit'] = 0;
    return self::getList($hql, $query);
  }

  static public function getList($hql,) {
    return model(self::$table)->getList();
  }

  static public function getInfo($condition) {
    if(_::isInteger($condition)) {
      $condition = ['id'=>$condition];
    }
    return model(self::$table)->getInfo($condition);
  }

}

?>