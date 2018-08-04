<?php
  include_once __DIR__.'/_.php';
  //error_reporting(E_ALL^E_NOTICE);
  /**
   * php版参数校验类
   * 作者: 阮家友
   * 时间: 2018-7-10 01:28:10
   * 联系: 1439120442@qq.com
   *  git: https://github.com/ruanjiayou
   */
  // 修改默认的异常处理器
  //set_exception_handler("Hinter");

  class Validater {
    static public $types = ['required', 'nullable', 'object', 'empty', 'nonzero', 'ignore', 'default', 'alias', 'minlength', 'maxlength', 'length', 'min', 'max', 'methods', 'array', 'char', 'string', 'text', 'enum', 'int', 'float', 'file', 'boolean', 'date', 'dateonly', 'timeonly'];
    static public $atoms = ['array', 'object', 'char', 'string', 'text', 'enum', 'int', 'float', 'file', 'boolean', 'date', 'dateonly', 'timeonly'];
    static public $bools = [1, '1', true, 'TRUE'];
    static public $messages = array(
      'zh-cn' => array(
        'atom'=>'验证规则{{value}}中有且只有一种基本类型!',
        'required'=> '{{field}} 字段不能为空!',
        'date'=> '{{field}} 字段的值 {{data}} 不是有效的 日期时间 格式!',
        'dateonly'=> '{{field}} 字段的值 {{data}} 不是有效的日期格式!',
        'timeonly'=> '{{field}} 字段的值 {{data}} 不是有效的时间格式!',
        'custom'=> '{{data}} 不是 {{field}} 字段中 自定义的验证方法 {{value}}!',
        'methods'=> array(),
        'int'=> '{{field}} 字段的值 {{data}} 必须是整数!',
        'float'=> '{{field}} 字段的值 {{data}} 不是有效的浮点数!',
        'float.m'=> '{{field}} 字段的值 {{data}} 整数位数多于限定值 {{value}}!',
        'float.n'=> '{{field}} 字段的值 {{data}} 小数位数多于限定值 {{value}}!',
        'boolean'=> '{{field}} 字段的值 {{data}} 不是布尔类型!',
        'enum'=> '{{field}} 字段的值 {{data}} 不是{{rule}} 规则中 {{value}} 中的一种!',
        'array'=> '{{field}} 字段的值 {{data}} 不是数组!',
        'object'=> '{{field}} 字段的值 {{data}} 不是对象!',
        'min'=> '{{field}}的值最小为{{value}}!',
        'max'=> '{{field}}的值最大为{{value}}!',
        'minlength'=> '{{field}}的长度最小为{{value}}!',
        'maxlength'=> '{{field}}的长度最大为{{value}}!',
        'length'=> '{{field}}的长度不是{{value}}!',
        'file'=> '{{data}} 不是预期({{value}})的文件格式!'
      )
    );
    public $lang;
    public $methods;
    public $rules;
    /**
     * 
     */
    function __construct($rules = array(), $methods = array(), $lang = 'zh-cn') {
      $this->lang = $lang;
      $this->methods = $methods;
      $this->rules = $this->parse($rules);
    }
    /**
     * 辅助函数: 字符串转数组
     * @param {string} $str 字符串
     * @param {string} $separator 分割符
     * @param {function} 回调函数
     */
    function _str2arr($str, $sperator = ',', $cb = null) {
      $arr = array_map(function($item){
        return trim($item);
      }, explode($sperator, $str));
      return $arr;
    }
    /**
     * 将数据编译到模板
     * @param {string} $str 模板
     * @param {object} $data 数据
     */
    function compile($str, $data) {
      $res = $str;
      preg_match_all('(\{\{\s*([a-z0-9A-Z]+)\s*\}\})', $str, $arr);
      $raws = $arr[0];
      $keys = $arr[1];
      for($i=0;$i<count($raws);$i++){
        $key = $keys[$i];
        $value = $data[$key] ? $data[$key] : ' ?? ';
        $res = str_replace($raws[$i], $value, $res);
      }
      return $res;
    }
    /**
     * 统一错误处理
     * @param {object} o 错误详情
     */
    function error($o, $data = null) {
      if(!is_string($o)) {
        $o['message'] = $this->compile(self::$messages[$this->lang][$o['rule']], $o);
      }
      $hinter = new Hinter();
      $hinter->setHinter($o, $data);
      throw $hinter;
    }
    /**
     * 过滤并验证参数(使filter()和check()两个函数的串联)
     * @param {object} data 待校验数据
     */
    function validate($data) {
      return $this::check($this::filter($data));
    }
    /**
     * 按rules的字段过滤额外的字段(将布尔类型的转为true/false)
     * @param {object} 原数据
     */
    function filter($data) {
      $res = array();
      foreach($data as $k => $v) {
        if($this->rules[$k]) {
            $rule = $this->rules[$k];
            if($rule['boolean']) {
              $res[$k] = in_array($data[$k], self::$bools) ? true : false;
            } else {
              $res[$k] = $data[$k];
            }
        }
      }
      return $res;
    }

    /**
     * 字符串转规则对象
     * @param {string} $str 字符串规则
     * @returns {object} 增强的规则对象
     */
    function _str2rule($str) {
      $arr = $this::_str2arr($str, '|');
      $hasAtom = false;
      $rule = array(
        'nullable' => false,
        'nonzero' => false,
        'empty' => false,
        'ignore' => false,
        'boolean' => false,
        'int' => false,
        'float' => false,
        'date' => false,
        'dateonly' => false,
        'timeonly' => false,
        'methods' => []
      );
      for($i = 0; $i < count($arr); $i++) {
        $s = $arr[$i];
        preg_match_all("/^([a-zA-Z0-9]+)[:]?(.*)$/", $s, $match);
        $k = $match[1][0];
        $v = $match[2][0];
        // 检查: 至少一个基本类型
        if($hasAtom == false && in_array($k, self::$atoms)) {
          if($hasAtom === false) {
            $hasAtom = true;
          } else {
            $this->error(['rule'=>'atom','value'=>$str,'message'=>'']);
          }
        }
        if('file' == $k) {
          $rule['file'] = $this->_str2arr($v);
        } else if('methods' == $k) {
          $rule['methods'] = array();
          $methods = $this->_str2arr($v);
          foreach($methods as $method) {
            if($this->methods[$method]) {
              $rule['methods'][$method] = $this->methods[$method];
            }
          }
        } else if('min' == $k) {
          $rule['min'] = floatval($v);
        } else if('max' == $k) {
          $rule['max'] = floatval($v);
        } else if('float' == $k) {
          if(trim($v) == ''){
            $rule['float'] = array('m'=>10,'n'=>2);
          } else {
            $mn = $this->_str2arr($v);
            $rule['float'] = array('m'=>intval($mn[0]), 'n'=>intval($mn[1]));
          }
        } else if('minlength' == $k) {
          $rule['minlength'] = intval($v);
        } else if('maxlength' == $k) {
          $rule['maxlength'] = intval($v);
        } else if('length' == $k) {
          $rule['length'] = intval($v);
        } else if('enum' == $k) {
          $rule['enum'] = $this->_str2arr($v);
        } else if('default' == $k) {
          $rule['default'] = ['type'=>'value', 'value'=>''];
          switch($v) {
            case '0': $rule['default']['value'] = 0;break;
            case '1': $rule['default']['value'] = 1;break;
            case 'true': $rule['default']['value'] = true;break;
            case 'false': $rule['default']['value'] = false;break;
            case 'null': $rule['default']['value'] = null;break;
            case 'array': $rule['default']['value'] = [];break;
            case 'timestamp': $rule['default']['value'] = time();break;
            case 'timestamps':
              list($t1, $t2) = explode(' ', microtime());
              $rule['default']['value'] = floor((floatval($t1)+floatval($t2))*1000);
              break;
            case 'date': $rule['default']['value'] = date('Y-m-d');break;
            case 'time': $rule['default']['value'] = date('H:i:s');break;
            case 'datetime': $rule['default']['value'] = date('Y-m-d H:i:s');break;
            case 'today': $rule['default']['value'] = date('Y-m-d').' 00:00:00';break;
            default:
              preg_match('/^([\'\"\(])(.*)(\1|\))$/',$v, $match);
              if(4 == count($match)) {
                if($match[1] == '(') {
                  $rule['default']['type'] = 'function';
                }
                $rule['default']['value'] = $match[2];
              } else {
                unset($rule['default']);
              }
            break;
          }
        } else if('alias' == $k) {
          $rule['alias'] = $v;
        } else {
          $rule[$k] = true;
        }
      }
      if($hasAtom == false) {
        $this->error(['rule'=>'atom', 'message'=>'']);
      }
      return $rule;
    }
    /**
     * 将简易字符串验证转化为增强的对象
     * @param {object} $rules 验证对象
     */
    function parse($rules) {
      $res = array();
      foreach($rules as $k => $v) {
        $res[$k] = $this::_str2rule($v);
      }
      return $res;
    }
    /**
     * 校验字段(default最强,function类型是后置的;能传null必须声明nullable,能传空'',必须声明empty,不能传0必须声明nonzero)
     * @param {object} $data 数据
     * @returns {object} 处理后的数据
     */
    function check($data) {
      foreach($this->rules as $k => $rule) {
        if(_::isObject($rule['default']) && 'value' == $rule['default']['type']) {
          $data[$k] = $rule['default']['value'];
        }
        if(!isset($data[$k])) {
          $data[$k] = null;
        }
        $v = $data[$k];
        $err = array('field'=>$k, 'data'=>$v, 'rule'=>'', 'value'=>'', 'message'=>'');
        // undefined null '' 的处理:required处理undefined;nullable处理null;empty 处理 空字符串
        if(null == $v && $rule['nullable'] || $v == '' && $rule['empty']) {
          continue;
        }
        if(_::isEmpty($v)) {
          if($rule['required']) {
            $err['rule'] = 'required';
            $err['value'] = $v;
            $this->error($err, $data);
          } else {
            unset($data[$k]);
            continue;
          }
        }
        if($rule['nonzero'] && ($v === 0 || $v === '0')) {
          $err['rule'] = 'nonzero';
          $this->error($err, $data);
        }
        if($rule['int']) {
          if(!is_numeric($v)) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'int';
            $this->error($err, $data);
          }
          $v = floatval($v);
        }
        if($rule['float']) {
          $regstr = '/^([0-9]+)(.([0-9]+))?$/';
          preg_match($regstr, $v, $mn);
          if(!is_numeric($v) || count($mn) == 0) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'float';
            $this->error($err);
          }
          $m = strlen($mn[1]);
          $n = count($mn) == 4? strlen($mn[3]) : 0;
          if($m > $rule['float']['m']) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'float.m';
            $err['value'] = $rule['float']['m'];
            $this->error($err);
          }
          if($n > $rule['float']['n']) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'float.n';
            $err['value'] = $rule['float']['n'];
            $this->error($err);
          }
          $v = floatval($v);
        }
        if(isset($rule['min']) && $v < $rule['min']){
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $err['rule'] = 'min';
          $this->error($err, $data);
        }
        if(isset($rule['max']) && $v > $rule['max']) {
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $err['rule'] = 'max';
          $this->error($err, $data);
        }
        if(isset($rule['array']) && $rule['array'] && is_array($rule['array']) && !is_array($v)) {
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $err['rule'] = 'array';
          $this->error($err, $data);
        }
        if(isset($rule['object']) && !_::isObject($data)) {
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $err['rule'] = 'object';
          $this->error($err, $data);
        }
        if(is_string($v) || is_array($v)) {
          $len = is_string($v) ? strlen($v) : count($v);
          if(isset($rule['minlength']) && $rule['minlength'] && $len < $rule['minlength']) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'minlength';
            $err['value'] = $rule['minlength'];
            $this->error($err, $data);
          }
          if(isset($rule['maxlength']) && $rule['maxlength'] && $len > $rule['maxlength']) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'maxlength';
            $err['value'] = $rule['maxlength'];
            $this->error($err, $data);
          }
          if(isset($rule['length']) && $rule['length'] && $len != $rule['length']) {
            if(!$rule['required'] && $rule['ignore']) {
              continue;
            }
            $err['rule'] = 'length';
            $err['value'] = $rule['length'];
            $this->error($err, $data);
          }
        }
        if(isset($rule['enum']) && !in_array($v, $rule['enum'])) {
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $err['rule'] = 'enum';
          $err['value'] = implode(',', $rule['enum']);
        }
        if($rule['boolean']) {
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $v = in_array($v, self::$bools) ? true : false;
        }
        //TODO: 日期验证
        //TODO: 文件

        $isError = false;
        foreach($rule['methods'] as $f => $fn) {
          $res = $fn($v);
          if(!$res) {
            $err['rule'] = 'methods';
            $err['value'] = $f;
            $isError = true;
          }
        }
        if($isError) {
          if(!$rule['required'] && $rule['ignore']) {
            continue;
          }
          $this->error($err, $data);
        }
        $data[$k] = $v;
        if(isset($rule['default']) && 'function' == $rule['default']['type']) {
          $func = $rule['default']['value'];
          if('toString' == $func) {
            $data[$k] = json_encode($v);
          }
        }
        if(isset($rule['alias']) && $rule['alias']) {
          $alias = str_replace('%', $k, $rule['alias']);
          $data[$alias] = $data[$k];
          unset($data[$k]);
        }
      }
      return $data;
    }
    /**
     * 其他内置函数 isUrl() isInt 
     */
  }

?>