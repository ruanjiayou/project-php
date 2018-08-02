<?php
namespace app\model;
use think\Model;

class ModelBase extends Model {
    public $primaryKey = 'id';

    public function add($data) {
        $id = db($this->name)->insertGetId($data);
        $condition = array();
        $condition[$this->primaryKey] = $id;
        return $this->getInfo($condition);
    }

    public function remove($condition) {
        return db($this->name)->where($condition)->delete();
    }

    public function edit($condition, $data) {
        db($this->name)->where($condition)->update($data);
        return $this->getInfo($condition);
    }

    public function getInfo($condition, $opt = []) {
        $order = isset($opt['order']) ? $opt['order'] : $this->primaryKey.' DESC';
        $field = isset($opt['field']) ? $opt['field'] : '*';
        $exclude = false;
        if($field[0] === '!') {
            $exclude = true;
            $field = substr($field, 1);
        }
        return db($this->name)->where($condition)->field($field, $exclude)->order($order)->find();
    }

    public function getList($opts=array()) {
        $where = isset($opts['where']) ? $opts['where'] : [];
        $field = isset($opts['field']) ? $opts['field'] : '*';
        $exclude = false;
        if($field[0] === '!') {
            $exclude = true;
            $field = substr($field, 1);
        }
        $order = isset($opts['order']) ? $opts['order'] : $this->primaryKey.' DESC';
        $limit = isset($where['limit']) ? $where['limit'] : 10;
        $page = isset($where['page']) ? $where['page'] : 1;
        unset($where['page']);
        unset($where['limit']);
        if($limit === 0) {
            return db($this->name)->where($where)->field($field, $exclude)->order($order)->select();
        } else {
            return db($this->name)->where($where)->field($field, $exclude)->order($order)->paginate($limit,false,['page'=>$page]);
        }
    }
}
