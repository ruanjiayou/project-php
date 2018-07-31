<?php

function loadErrors($dir) {
  $returns = [];
  $dh = opendir($dir);
  // 遍历语言包
  while(($file=readdir($dh))!==false) {
    $fullpath = $dir.$file;
    $lang = $file;
    if($file !== '.' && $file !== '..' && is_dir($fullpath)) {
      // 语言包
      $returns[$lang] = [];
      $dh2 = opendir($fullpath);
      // 遍历模块
      while(($file2=readdir($dh2))!==false) {
        $fullpath2 = $fullpath.'/'.$file2;
        if($file2 !== '.' && $file2 !== '..' && is_file($fullpath2)) {
          $module = basename(strtolower($fullpath2), '.php');
          $returns[$lang][$module] = require_once($fullpath2);
        }
      }
      closedir($dh2);
    }
  }
  closedir($dh);
  return $returns;
}

class hinterErrors {
  static $errors = [];
}
hinterErrors::$errors = loadErrors(__DIR__.'/../errors/');

function thrower($module, $type, $lang = 'zh-cn') {
  $hinter = new Hinter();
  $found = isset(hinterErrors::$errors[$lang]) && isset(hinterErrors::$errors[$lang][$module]) && isset(hinterErrors::$errors[$lang][$module][$type]);
  $message = $found ? hinterErrors::$errors[$lang][$module][$type]['message'] : '自定义错误数据没找到!';
  $code = $found ? hinterErrors::$errors[$lang][$module][$type]['code'] : 0;
  $hinter->info = [
    R_STATUS => R_FAIL,
    R_DATA => null,
    R_CODE => $code,
    R_ERROR => $message,
    R_STACK => ['module' => $module, 'type' => $type]
  ];
  throw $hinter;
}

?>