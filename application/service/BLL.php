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
    if('string' === $type || 'integer' === $type) {
      $condition = [$pk=>$condition];
    }
    if('array' === $type) {
      $condition = [$pk => ['in', $condition]];
    }
    return model($this->table)->remove($condition);
  }

  public function update($data, $condition) {
    //TODO: 数据不存在 错误不明
    // $validation = new Validater([
    //   'name' => 'required|string'
    // ]);
    // $data = $validation->validate($data);
    return model($this->table)->edit($condition, $data);
  }

  public function getAll($hql=[]) {
    $hql['limit'] = 0;
    return $this->getList($hql);
  }

  public function getList($hql) {
    return model($this->table)->getList($hql);
  }

  public function getInfo($condition, $opts=[]) {
    return model($this->table)->getInfo($condition, $opts);
  }

}

?>