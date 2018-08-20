<?php
namespace app\model;
use think\Model;

class ModelBase extends Model {
    public $primaryKey = 'id';

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
        $exclude = false;
        if($field[0] === '!') {
            $exclude = true;
            $field = substr($field, 1);
        }
        if(is_integer($condition) || is_string($condition)) {
            $condition = [$this->primaryKey=>$condition];
        }
        return db($this->name)->where($condition)->field($field, $exclude)->order($order)->find();
    }

    function getList($opts=array()) {
        $where = isset($opts['where']) ? $opts['where'] : [];
        $field = isset($opts['field']) ? $opts['field'] : '*';
        $whereOr = isset($opts['whereOr']) ? $opts['whereOr'] : [];
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
        if($limit === 0) {
            return db($this->name)->where($where)->whereOr($whereOr)->field($field, $exclude)->order($order)->select();
        } else {
            return db($this->name)->where($where)->whereOr($whereOr)->field($field, $exclude)->order($order)->paginate($limit,false,['page'=>$page]);
        }
    }
}

?>