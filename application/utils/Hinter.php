<?php
  /**
   * php版参数校验类
   * 作者: 阮家友
   * 时间: 2018-7-10 01:28:10
   * 联系: 1439120442@qq.com
   *  git: https://github.com/ruanjiayou
   */
  class Hinter extends Exception {
    static public $lang = 'zh-cn';
    public $info;
    public function setHinter($o, $data) {
      $this->info = array(
        R_STATUS => R_FAIL,
        R_DATA => $data,
        R_CODE => 1,
        R_ERROR => $o['message'],
        R_STACK => $o
      );
      return $this;
    }
  }
?>