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
// 支付宝
define('C_ALI_APPID', '');
define('C_ALI_PRIVATEKEY', '');
define('C_ALI_PUBLICKEY', '');
define('C_ALI_PAYCB', '');
// 腾讯云短信
define('C_TX_SMS_APPID', '1400120461');
define('C_TX_SMS_APPKEY', 'ce80ec5dc84a0e03bc4ee0b74f53f4fa');
// 腾讯云对象存储
define('C_TX_COS_APPID', '1257241165');
define('C_TX_COS_SECRETID', 'AKIDALQMDvpq4rYKxPEBJG6EFE2d2GIA6XIr');
define('C_TX_COS_SECRETKEY', 'Yu695sZHpDQRGyVr03wefWDiYJtNfmlf');
define('C_TX_COS_BUCKET', 'banyou');
define('C_TX_COS_REGION', 'sh');

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