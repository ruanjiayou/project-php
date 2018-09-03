<?php
//TODO: 先列出了所有函数,待补充完整
class _ {

  static function log() {
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
  static function type($o) {
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
  
  static function isFunction($o) {
    return 'function' === _::type($o);
  }

  static function isArray($o) {
    return 'array' === _::type($o);
  }
  
  static function isString($o) {
    return 'string' === _::type($o);
  }
  
  static function isDate($o) {
    
  }
  
  static function isInt($o) {
    $t = _::type($o);
    return 'integer' === $t;
  }
  
  static function isFloat($o) {
    $t = _::type($o);
    return 'float' === $t || 'double' === $t;
  }
  
  static function isNumber() {
    $t = _::type($o);
    return 'integer' === $t || 'float' === $t || 'double' === $t;
  }

  static function isNumbric($o) {
    return preg_match("/^\d+(\.\d+)?$/",$o);
  }
  
  static function isBoolean($o) {
    return 'boolean' === _::type($o);
  }
  
  static function isRegExp($o) {

  }
  
  static function isObject($o) {
    return 'object' === _::type($o);
  }
  
  /**
   * 判断是否为空, empty未定义 '' 0 '0' null
   */
  static function isEmpty($o) {
    return $o === null || $o === '';
  }
  
  /**
   * 判断数组是否为空对象
   */
  static function isEmptyObject($o) {
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
  
  static function isError() {

  }
  
  /**
   * 返回键数组
   */
  static function keys($o) {
    $res = [];
    if('object' === _::type($o)) {
      $res = array_keys($o);
    }
    return $res;
  }

  /**
   * 浅克隆
   */
  static function deepClone($o) {
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
  static function pick($o, $arr) {
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
  static function filter($o, $arr) {
    $oo = _::deepClone($o);
    if('array' === _::type($arr)) {
      foreach($arr as $k) {
        unset($oo[$k]);
      }
    }
    return $oo;
  }

  /**
   * 合并返回新的
   */
  static function assign($o, $d) {
    $result = self::deepClone($o);
    if(self::isObject($d)) {
      foreach($d as $k => $v) {
        $result[$k] = $v;
      }
    }
    return $result;
  }

  /**
   * 合并无返回
   */
  static function assignIn($o, $d) {
    if(self::isObject($o) && self::isObject($d)) {
      foreach($d as $k => $v) {
        $o[$k] = $v;
      }
    }
  }
  
  static function compare($str1, $str2) {
    return strcmp($str1, $str2);
  }
  
  static function isBefore($d1, $d2) {
    return self::compare($d1, $d2) < 0 ? true : false;
  }

  static function sortBy($cb) {

  }
  
  /**
   * 生成随机字符串
   * @param {int} $len 长度,默认32,最小长度为6
   * @param {string} $type 类型,number,imix,mix,char,ichar
   */
  static function random($len = 32, $type = 'number') {
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
      $res .= $chs[mt_rand(0,$l)];    //生成php随机数   
    }   
    return $res;
  }

  /**
   * 字符串替换
   */
  static function replace($str, $pattern, $replace) {
    return str_replace($pattern, $replace, $str);
  }

  /**
   * 文件对象转base64
   */
  static function file2base64($o) {
    $filepath = $o;
    $res = '';
    if($fp = fopen($filepath, 'rb', 0)) {
      $data = fread($fp, filesize($filepath));
      fclose($fp);
      $res = self::binary2base64($data);
    }
    return $res;
  }

  /**
   * 二进制转base64
   */
  static function binary2base64($bins) {
    return chunk_split(base64_encode($bins));
  }
}
?>