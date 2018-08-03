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

  public function destroy($data) {
    $validation = new Validater([
      'id' => 'required|array'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->remove(['id'=>['in', $input['id']]]);
  }

  public function update($data, $condition) {
    // $validation = new Validater([
    //   'name' => 'required|string'
    // ]);
    // $data = $validation->validate($data);
    if(_::isInt($condition) || _::isString($condition)) {
      $condition = ['id'=>$condition];
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