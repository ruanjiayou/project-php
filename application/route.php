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

include_once __DIR__.'/utils/CustomRoute.php';
include_once __DIR__.'/utils/Hinter.php';
include_once __DIR__.'/utils/thrower.php';
include_once __DIR__.'/utils/Validater.php';

// 加载所有route
CustomRoute::loadAll(['dir'=>__DIR__.'/routes']);
?>