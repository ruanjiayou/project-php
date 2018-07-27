<?php
  /**
   * php版参数校验类
   * 作者: 阮家友
   * 时间: 2018-7-10 01:28:10
   * 联系: 1439120442@qq.com
   *  git: https://github.com/ruanjiayou
   */
  class Hinter extends Exception {
    public $info;
    public function setHinter($o, $data) {
      $this->info = array(
        'status' => 'success',
        'data' => $data,
        'error' => $o['message'],
        'stack' => $o
      );
      return $this;
    }
  }
?>