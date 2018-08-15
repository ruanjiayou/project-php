<?php
use \Firebase\JWT\JWT;
use think\Request;

class BLL {
  
  public $table = '';
  public $strict = false;

  function __construct($strict = false) {
    $this->strict = $strict;
  }

  function strict() {
    $this->strict = true;
  }

  public function create($data) {
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
    $result = model($this->table)->edit($condition, $data);
    if($this->strict === true && null === $result) {
      thrower('common', 'notFound');
    }
    return $result;
  }

  public function getAll($hql=[]) {
    $hql['limit'] = 0;
    return $this->getList($hql);
  }

  public function getList($hql) {
    return model($this->table)->getList($hql);
  }

  public function getInfo($condition, $opts=[]) {
    $result = model($this->table)->getInfo($condition, $opts);
    if($this->strict === true && null === $result) {
      thrower('common', 'notFound');
    }
    return $result;
  }

}

?>