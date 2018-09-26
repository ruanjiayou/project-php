<?php

class myHttp {
  protected $curl = null;
  protected $type = '';
  protected $urlInfo = '';
  protected $query = [];
  protected $files = [];
  public $body = null;
  function __construct($url, $type = 'get') {
    // 提取query参数
    $this->urlInfo = parse_url($url);
    if(isset($this->urlInfo['query'])) {
      parse_str($this->urlInfo['query'], $this->query);
    }
    $this->curl = curl_init();
    $this->type = $type;
    //curl_setopt($this->curl, CURLOPT_URL, $url);
    // 不要http header 加快效率
    //curl_setopt($this->curl, CURLOPT_HEADER, 1);
    curl_setopt($this->curl, CURLOPT_HEADER, false);//返回的header去掉 不然返回的$result不能解析为json
    $header[] = 'Content-Type:application/json;charset=utf-8';
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
    // curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json;charset=utf-8']);
    // 结果是保存到字符串还是输出到屏幕
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    // 是否获取跳转后的页面
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
    //SSL验证            
    //curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($this->curl, CURLOPT_SSLVERSION_SSL, 2);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);// https请求时要设置为false 不验证证书和hosts  FALSE 禁止 cURL 验证对等证书（peer's certificate）, 自cURL 7.10开始默认为 TRUE。从 cURL 7.10开始默认绑定安装。 
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, FALSE);// 检查服务器SSL证书中是否存在一个公用名(common name)。
    // 设置超时
    curl_setopt ($this->curl, CURLOPT_CONNECTTIMEOUT, 15);
  }
  /**
   * 设置参数
   */
  function opt($param) {
    foreach($param as $k => $v) {
      curl_setopt($this->curl, $k, $v);
    }
    return $this;
  }
  /**
   * 设置querystring参数, null 就是删除参数
   */
  function query($param) {
    if(_::isObject($param)) {
      foreach($param as $k => $v) {
        if($v === null) {
          unset($this->query[$k]);
        } else {
          $this->query[$k] = $v;
        }
      }
    }
    return $this;
  }
  /**
   * 设置body参数
   */
  function send($body) {
    $this->body = $body;
    return $this;
  }
  /**
   * 添加文件
   */
  function attach($files) {
    foreach($files as $k => $v) {
      if(_::isArray($v)) {
        $this->files[$k];
        for($i=0;$i<count($v);$i++) {
          $this->files[$k.'['.$i.']'] = new CURLFile($v[$i]);
        }
      } else {
        $this->files[$k] = new CURLFile($v);
      }
    }
    return $this;
  }
  /**
   * 设置cookie
   */
  function cookie() {
    $str = session_name().'='.session_id().';';
    session_write_close();
    curl_setopt($this->curl, CURLOPT_COOKIE, $str);
    return $this;
  }
  /**
   * 发送请求获取返回
   * @param $datatype xml/json/string
   * TODO: 文件/图片转base64/callback
   */
  function end($datatype = 'json') {
    // 最终url
    $qs = http_build_query($this->query);
    $url = $this->urlInfo['scheme'] . '://' . $this->urlInfo['host'] . $this->urlInfo['path'] . ($qs!=='' ? '?'.$qs : '');
    curl_setopt($this->curl, CURLOPT_URL, $url);
    curl_setopt($this->curl, CURLINFO_HEADER_OUT,1);//启用时追踪句柄的请求字符串。
    // 最终body
    if($this->body!==null) {
      foreach($this->files as $name => $file) {
        $this->body[$name] = $file;
      }
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($this->body));
    }
    $result = curl_exec($this->curl);
    $headers = curl_getinfo($this->curl, CURLINFO_HEADER_OUT);// 响应头
    curl_close($this->curl);
    // 响应处理
    if($datatype === 'json') {
      $result = json_decode($result, true);
    }
    if($datatype === 'xml') {
      $result = json_decode(json_encode(simplexml_load_string($result)), true);
    }
    if($datatype === 'string') {

    }
    return $result;
  }
}

class shttp {
  static function get($url) {
    $mh = new myHttp($url, 'get');
    $mh->opt([CURLOPT_HTTPGET=>true]);
    return $mh;
  }
  static function put($url) {
    $mh = new myHttp($url, 'put');
    $mh->opt([CURLOPT_CUSTOMREQUEST=>'PUT']);
    return $mh;
  }
  static function post($url) {
    $mh = new myHttp($url, 'post');
    $mh->opt([CURLOPT_POST=>1]);
    return $mh;
  }
  static function delete($url) {
    $mh = new myHttp($url, 'delete');
    $mh->opt([CURLOPT_CUSTOMREQUEST=>'DELETE']);
    return $mh;
  }
}

?>