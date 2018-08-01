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

    public function getInfo($condition) {
        unset($condition['page']);
        unset($condition['limit']);
        return db($this->name)->where($condition)->find();
    }

    public function getList($opts=array()) {
        $where = $opts['where'];
        $field = isset($opts['field']) ? $opts['field'] : '*';
        $order = isset($opts['order']) ? $opts['order'] : $this->primaryKey.' DESC';
        $limit = isset($where['limit']) ? $where['limit'] : 10;
        $page = isset($where['page']) ? $where['page'] : 1;
        unset($where['page']);
        unset($where['limit']);
        if($limit === 0) {
            return db($this->name)->where($where)->field($field)->order($order)->select();
        } else {
            return db($this->name)->where($where)->field($field)->order($order)->paginate($limit,false,['page'=>$page]);
        }
    }
}
