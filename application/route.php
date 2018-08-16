<?php

define('R_STATUS', 'state');
define('R_SUCCESS', 'success');
define('R_FAIL', 'fail');
define('R_DATA', 'rdata');
define('R_CODE', 'ecode');
define('R_ERROR', 'error');
define('R_STACK', 'stack');
define('R_PAGENATOR', 'paginator');
define('R_PAGENATOR_PAGE', 'page');
define('R_PAGENATOR_PAGES', 'pages');
define('R_PAGENATOR_LIMIT', 'limit');
define('R_PAGENATOR_COUNT', 'count');
define('R_PAGENATOR_TOTAL', 'total');
define('R_ORDER', 'order');
define('R_SEARCH', 'search');
# 业务逻辑常量
// 密匙
define('C_AUTH_KEY', 'ssasbbs');
// 过期时间
define('C_AUTH_EXPIRED', 60*60*24*7);
// 佣金比例
define('C_MONEY_PLATFOM', 0.2);
define('C_MONEY_AGENCY', 0.1);
define('C_MONEY_SELLER', 0.7);
// 取消扣钱
define('C_PUNISHMENT1_M', 5);
define('C_PUNISHMENT1_V', 50);
define('C_PUNISHMENT2_M', 15);
define('C_PUNISHMENT2_V', 100);
// 支付宝
define('C_ALI_APPID', '');
define('C_ALI_PRIVATEKEY', '');
define('C_ALI_PUBLICKEY', '');
define('C_ALI_PAYCB', '');

include_once __DIR__.'/service/BLL.php';
include_once __DIR__.'/plugin/alipay-sdk/AopSdk.php';

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