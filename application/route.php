<?php

define('R_STATUS', 'state');
define('R_SUCCESS', 'success');
define('R_FAIL', 'fail');
define('R_DATA', 'rdata');
define('R_CODE', 'ecode');
define('R_ERROR', 'error');
define('R_STACK', 'stack');
define('R_PAGENATOR', 'pagination');
define('R_PAGENATOR_PAGE', 'page');
define('R_PAGENATOR_PAGES', 'pages');
define('R_PAGENATOR_LIMIT', 'limit');
define('R_PAGENATOR_COUNT', 'count');
define('R_PAGENATOR_TOTAL', 'total');
define('R_ORDER', 'order');
define('R_SEARCH', 'search');

define('C_AUTH_KEY', 'ssasbbs');
define('C_AUTH_EXPIRED', 60*60*24*7);

function autoLoad($dir) {
  $dh = opendir($dir);
  while(($file=readdir($dh))!==false) {
    $fullpath = $dir.$file;
    if($file!=='.' && $file!=='..' && is_file($fullpath)) {
      include_once $fullpath;
    }
  }
  closedir($dh);
}

autoLoad(__DIR__.'/utils/');
autoLoad(__DIR__.'/service/');

// 加载所有route
CustomRoute::loadAll(['dir'=>__DIR__.'/routes']);
?>