<?php
//TODO: 先列出了所有函数,待补充完整
class _ {

  static public function log() {
    $args = func_get_args();
    dump($args);
    // for($i=0;$i<count($args);$i++) {
    //   dump($args[$i]);
    // }
  }
  
  /**
   * 获取变量类型
   * @param {object} $o 
   * integer boolean array object function null double resource
   */
  static public function type($o) {
    $t = gettype($o);
    if($t === 'NULL') {
      $t = 'null';
    }
    if($t === 'object' && is_callable($o)) {
      $t = 'function';
    }
    if($t === 'array' && array_diff_assoc(array_keys($o), range(0, sizeof($o)))) {
      $t = 'object';
    }
    return $t;
  }
  
  static public function isFunction($o) {
    return 'function' === _::type($o);
  }

  static public function isArray($o) {
    return 'array' === _::type($o);
  }
  
  static public function isString($o) {
    return 'string' === _::type($o);
  }
  
  static public function isDate($o) {
    
  }
  
  static public function isNumber($o) {
    $t = _::type($o);
    return 'integer' === $t || 'double' === $t;
  }
  
  static public function isBoolean($o) {
    return 'boolean' === _::type($o);
  }
  
  static public function isRegExp($o) {

  }
  
  static public function isObject($o) {
    return 'object' === _::type($o);
  }
  
  /**
   * 判断是否为空, 未定义 '' 0 '0' null
   */
  static public function isEmpty($o) {
    return empty($o);
  }
  
  /**
   * 判断数组是否为空对象
   */
  static public function isEmptyObject($o) {
    $res = true;
    $t = _::type($o);
    if($t === 'array' || $t === 'object') {
      foreach($o as $k) {
        $res = false;
        break;
      }
    }
    return $res;
  }
  
  static public function isError() {

  }
  
  /**
   * 返回键数组
   */
  static public function keys($o) {
    $res = [];
    if('object' === _::type($o)) {
      $res = array_keys($o);
    }
    return $res;
  }

  /**
   * 浅克隆
   */
  static public function deepClone($o) {
    $res = [];
    foreach($o as $k => $v) {
      $res[$k] = $o[$k];
    }
    return $res;
  }

  /**
   * 选取对象中指定的字段
   * @param {object} $o 对象
   * @param {array} $arr 字段数组
   */
  static public function pick($o, $arr) {
    $res = [];
    if(empty($o) || !is_array($arr)) {
      return $res;
    }
    foreach($o as $k => $v) {
      if(in_array($k, $arr)) {
        $res[$k] = $v;
      }
    }
    return $res;
  }

  /**
   * 过滤对象中指定的字段
   */
  static public function filter($o, $arr) {
    $oo = _::deepClone($o);
    if('array' === _::type($arr)) {
      foreach($arr as $k) {
        unset($oo[$k]);
      }
    }
    return $oo;
  }
  
  static public function compare() {

  }
  
  static public function sortBy($cb) {

  }
  
  /**
   * 生成随机字符串
   * @param {int} $len 长度,默认32,最小长度为6
   * @param {string} $type 类型,number,imix,mix,char,ichar
   */
  static public function random($len = 32, $type = 'number') {
    $chs = '';
    $res = '';
    if($type === 'mix') {
      $chs = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    } else if($type === 'imix') {
      $chs = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    } else if($type === 'char') {
      $chs = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    } else if($type === 'ichar') {
      $chs = 'ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    } else {
      $chs = '1234567890';
    }
    if($len<6) {
      $len = 6;
    }
    $l = strlen($chs);
    for($i=0;$i<$len;$i++) {   
      $res .= $chs{mt_rand(0,$l)};    //生成php随机数   
    }   
    return $res;
  }

}
?>