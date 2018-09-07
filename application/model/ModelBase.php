<?php
namespace app\model;
use think\Model;

class ModelBase extends Model {
  
  private function tran_scope($results, $scopes) {
    if(gettype($results)==='object') {
      return $results;
    }
    for($i=0;$i<count($scopes);$i++) {
      $scope = $scopes[$i];
      foreach($results as $result) {
        // 下面一句就可以
        $temp = collection($result[$scope])->toArray();
        //$result[$scope] = $temp['data'];
      }
    }
    return $results;
  }

  function add($data) {
    $pk = $this->getPK();
    $id = $this->insertGetId($data);
    $condition = array();
    $condition[$pk] = $id;
    return $this->getInfo($condition);
  }

  function remove($condition) {
    $pk = $this->getPK();
    if(is_integer($condition) || is_string($condition)) {
      $condition = [$pk=>$condition];
    }
    return $this->where($condition)->delete();
  }

  function edit($condition, $data) {
    $pk = $this->getPK();
    if(is_integer($condition) || is_string($condition)) {
      $condition = [$pk=>$condition];
    }
    $this->where($condition)->update($data);
    return $this->getInfo($condition);
  }

  function getInfo($condition, $opt = []) {
    $pk = $this->getPK();
    $field = isset($opt['field']) ? $opt['field'] : '*';
    $order = isset($opt['order']) ? $opt['order'] : 'id ASC';
    $exclude = false;
    if($field[0] === '!') {
      $exclude = true;
      $field = substr($field, 1);
    }
    if(is_integer($condition) || is_string($condition)) {
      $condition = [$pk=>$condition];
    }
    return $this->where($condition)->field($field, $exclude)->order($order)->find();
  }

  function getList($opts=array()) {
    $pk = $this->getPK();
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
      $where = [$pk=>$where];
    }
    $order = isset($opts['order']) ? $opts['order'] : $pk.' DESC';
    $limit = isset($opts['limit']) ? $opts['limit'] : 10;
    $page = isset($opts['page']) ? $opts['page'] : 1;
    $total = $this->where($where)->whereOr($whereOr)->field($field, $exclude)->order($order)->limit(($page-1)*$limit,$limit)->count();
    $results = $this->where($where)->whereOr($whereOr)->field($field, $exclude)->order($order)->limit(($page-1)*$limit,$limit)->select();
    if($limit === 0) {
      return $this->tran_scope($results, $scopes);
    } else {
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
}

?>