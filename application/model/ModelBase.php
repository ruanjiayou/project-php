<?php
namespace app\model;
use think\Model;

class ModelBase extends Model {
  public $primaryKey = 'id';

  private function tran_scope($results, $scopes) {
    if(gettype($results)==='object') {
      return $results;
    }
    for($i=0;$i<count($scopes);$i++) {
      $scope = $scopes[$i];
      foreach($results as $result) {
        $result[$scope] = collection($result[$scope])->toArray();
      }
    }
    return $results;
  }

  function add($data) {
    $id = db($this->name)->insertGetId($data);
    $condition = array();
    $condition[$this->primaryKey] = $id;
    return $this->getInfo($condition);
  }

  function remove($condition) {
    if(is_integer($condition) || is_string($condition)) {
      $condition = [$this->primaryKey=>$condition];
    }
    return db($this->name)->where($condition)->delete();
  }

  function edit($condition, $data) {
    if(is_integer($condition) || is_string($condition)) {
      $condition = [$this->primaryKey=>$condition];
    }
    db($this->name)->where($condition)->update($data);
    return $this->getInfo($condition);
  }

  function getInfo($condition, $opt = []) {
    $field = isset($opt['field']) ? $opt['field'] : '*';
    $order = isset($opt['order']) ? $opt['order'] : 'id ASC';
    $scopes = isset($opt['scopes']) ? $opt['scopes'] : [];
    $exclude = false;
    if($field[0] === '!') {
      $exclude = true;
      $field = substr($field, 1);
    }
    if(is_integer($condition) || is_string($condition)) {
      $condition = [$this->primaryKey=>$condition];
    }
    $results = $this->where($condition)->field($field, $exclude)->order($order)->find();
    return $this->tran_scope($results, $scopes);
  }

  function getList($opts=array()) {
      $where = isset($opts['where']) ? $opts['where'] : [];
      $field = isset($opts['field']) ? $opts['field'] : '*';
      $whereOr = isset($opts['whereOr']) ? $opts['whereOr'] : [];
      $scopes = isset($opts['scopes']) ? $opts['scopes'] : [];
      $exclude = false;
      if($field[0] === '!') {
        $exclude = true;
        $field = substr($field, 1);
      }
      if(is_integer($where) || is_string($where)) {
        $where = [$this->primaryKey=>$where];
      }
      $order = isset($opts['order']) ? $opts['order'] : $this->primaryKey.' DESC';
      $limit = isset($opts['limit']) ? $opts['limit'] : 10;
      $page = isset($opts['page']) ? $opts['page'] : 1;
      unset($where['page']);
      unset($where['limit']);
      $total = $this->where($where)->field($field, $exclude)->order($order)->limit(($page-1)*$limit,$limit)->count();
      $results = $this->where($where)->field($field, $exclude)->order($order)->limit(($page-1)*$limit,$limit)->select();
      
      return [
        'data'=>$this->tran_scope($results, $scopes),
        'total'=>$total,
        'limit'=>$limit,
        'page'=>$page,
        'pages'=> $limit==0 ? 1 : ceil($total/$limit),
        'count'=>count($results)
      ];
  }
}

?>