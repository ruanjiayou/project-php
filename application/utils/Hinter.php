<?php
  class Hinter extends Exception {
    public $info;
    public function setHinter($o) {
      $this->info = array(
        'status' => 1,
        'data' => null,
        'error' => $o['message'],
        'stack' => $o
      );
      return $this;
    }
  }
?>