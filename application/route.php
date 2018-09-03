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
// # 业务逻辑常量
// // 密匙
// define('C_AUTH_KEY', '');
// // 过期时间
// define('C_AUTH_EXPIRED', 60*60*24*7);
// // 支付宝
// define('C_ALI_APPID', '');
// define('C_ALI_PRIVATEKEY', '');
// define('C_ALI_PUBLICKEY', '');
// define('C_ALI_PAYCB', '');
// // 腾讯云短信
// define('C_TX_SMS_APPID', '');
// define('C_TX_SMS_APPKEY', '');
// // 腾讯云对象存储
// define('C_TX_COS_APPID', '');
// define('C_TX_COS_SECRETID', '');
// define('C_TX_COS_SECRETKEY', '');
// define('C_TX_COS_BUCKET', '');
// define('C_TX_COS_REGION', '');
$cfgs = model('config')->getList();
foreach($cfgs['data'] as $cfg) {
  $cfg = $cfg->getData();
  if($cfg['type'] === 'int') {
    $cfg['value'] = intval($cfg['type']);
  } elseif($cfg['type'] !== 'string') {
    $cfg['value'] = json_decode($cfg['type']);
  }
  define($cfg['name'], $cfg['value']);
}
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