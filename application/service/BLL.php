<?php
use \Firebase\JWT\JWT;
use think\Request;

class BLL {
  
  public $table = '';

  public function create($data) {
    // $validation = new Validater([
    //   'name' => 'required|string'
    // ]);
    // $input = $validation->validate($data);
    // return model('tag')->add($input);
    return model($this->table)->add($data);
  }

  public function destroy($condition) {
    $model= model($this->table);
    $pk = $model->primaryKey;
    $type = _::type($condition);
    if('string' === $condition || 'integer' === $condition) {
      $condition = [$pk=>$conditoin];
    }
    if('array' === $condition) {
      $condition = [$pk => ['in', $condition]];
    }
    return model($this->table)->remove($condition);
  }

  public function update($data, $condition) {
    // $validation = new Validater([
    //   'name' => 'required|string'
    // ]);
    // $data = $validation->validate($data);
    $model= model($this->table);
    $pk = $model->primaryKey;
    if(!_::isObject($condition)) {
      $condition = [$pk => $condition];
    }
    return model($this->table)->edit($condition, $data);
  }

  public function getAll($hql=[]) {
    $hql['limit'] = 0;
    return $this->getList($hql);
  }

  public function getList($hql) {
    return model($this->table)->getList($hql);
  }

  public function getInfo($condition) {
    return model($this->table)->getInfo($condition);
  }

}

?>