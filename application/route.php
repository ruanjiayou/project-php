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
define('C_ALI_APPID', '2018081461057335');
define('C_ALI_PRIVATEKEY', 'MIIEpQIBAAKCAQEA2to0GkP1W43IVQwR05sBLLGg5/gdq6VpCaUVRoAQUprPT/wfldEBhq/V8kN44e4FYYdqYcOB+T7IUz3sMiFErI/CY83Ewzcg5xTUtRFdcrj4I0gU1R6PzKfESZQW05GCyxQKJELhR2OwoKl6U4P0w0h3zhzsOKPhKjWJhfAdrZRLMcnVsNioErU469wdeTgNmzCkX3KLCuTzmEVNNWj/SlCwRoqaOOVIpOOKKVDbrrA02PELLoVO98jwMDm1l89fm8Mh0MKvdBz8lKKIcyX7TvEC5F0mn3dnWFYBZFRTA4kAlNi9c83U0iFlWWJQp6fEuRQGWDjV0Dfsw5BZ1ZA15QIDAQABAoIBAQCy1jBRbksnX3rfFJfUpQuWrChipnwIcYid2wrBq5CrD9ps8AgXcs2edD2OVNiJNumqwu+JqCujs0wbybQjWtA1etxMli89nuUCMCGJPQFx5/jNS+/KH8k+YbGitqLYjEWnEV8gNo7EuY/yGcveRyxSD/vmr/fQaQpsZYdVrdtypQLHrwkRnw9Io5BL7R75vkC3Am8iH4uM83UfhDJSSwypriEyA2zDO3DjfAi7PCPoLFHBqIuL463ZJ61EbR6yGHVF9WW4LI/S0a/3zniyJX8WDq/E4/3pqyJcN+4hSTVhAqwiKTuTdsrfdfKwN3khYALDksLVWl4C4uiUIBWGbovZAoGBAPGlOV8/ry4Un13pCIVVZs5sbJ6KzAnANvMn7BQf1J9q14E5inQYvettmOLqV33nz25C+T9LTBq4htPV67CUOuGr70ERe1kSqRYoZ0Xe/urlEK1QK0jB4LWjhRGhnziPSkrLtc64UuLRoujEd+9x5DbHYOxNtMsnac3Cok4/VLY/AoGBAOfaW8fxEhF4Ob2umhCWer3hlpwoCvyjYG2MLDQhMGCTsYnWCN1Fmd51jetn6GGqncRUZDTDeheSQ6hTo6gb/e1ypkkjxGNWiXva3OLzxAMUMlqqxKTxvyJ8AbFzCKsBxwvOe9iO77cIKs2pvMePYMZ6Hq72rBCPz0yylqLDgDLbAoGBAL5y/hi31JV443HcbD7J5FDk22bI9a7ps2VJHaNuwuEyD89lTl3Z8jVPF6QgFfzBapb4agEck0qsDHeArlVpPk8Gd3bNFG5LasBv75T9/+OZzd8KxFJ/m18NFZ+jxh2JsX/ptczLMWha2Q6jafNpy/fwg886HzfORHFK6SjKeTV5AoGAE68aM7Nn0UvfuxbjxZzA9vX8D23m4OQN/77y0covjUN8wzMEtaR/F7/rOJ0twXz2wABaMZCXAQFN9TCEqHUX7dzZ+UOsHLLwIS/HqQ0BCzHfxIrS/x33GDpm+mXFyp7wAzSYlx4rg+KRn7xVZqvpj3A2wqv2l8Fd3CkdWNw4OhkCgYEA1GTMfszUgm+2iyWzFque3b9EaF+TIgZm2wHoEhesEoZSBPKJ2qlTZCiVsMbm2ixn01NIAOMXsYv0e/hLr9Qo61YnI0dr/+4NO3zUEl/rFlFFPVaagwUW7Rkl2Ozh1pKl3uUpcjLq9eddO0TzePMsq1xLu77lBWMQLidvVsHF/Pg=');
define('C_ALI_PUBLICKEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhLP/rHw/vHsMsFMYZta+uq/CnyBXOMc+xgCfq4C7Hz5jKS7Xt8hlus0T99pbvW5xIy4xJFaW997lI++3unoLSqnDEKaeBvN3R86qd6oRv/Y3GqC31wEMnEqy/9gQ102qFRMsINzEHnKMPrYSQXQBC0l0hNKLMxtJotEbPaSLhGWV1DIC/9E78LS82cN1zxH73zJgALo0Wf9maL30PKG8X617kikupI5eVd7rK8kJ2ILx/gxECnx32n0RT2Y7ZZ4GB3sMfPxcqjqfo7qcVwrvtNnQL2PMh3EnjMo7IhOrASdr//rCngkDt1U3ri2Uf5RbRngEZ5sekb1N0kDbr2yA5wIDAQAB');
define('C_ALI_PAYCB', 'http://118.24.248.160/v1/public/alipay-cb');


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