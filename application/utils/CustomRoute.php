<?php
use think\Route;
use think\Request;
use think\Response;
use \Firebase\JWT\JWT;

Request::hook('auth', function($req){
  $token = $req->header('token');
  if($token === null) {
    thrower('token', 'tokenNotFound');
  }
  try {
    $token = (array)JWT::decode($token, C_AUTH_KEY, array('HS256'));
  } catch(Exception $e) {
    if($e->getMessage()==='Expired token') {
      thrower('token', 'tokenExpired');
    } else {
      thrower('token', 'tokenFormatError');
    }
  }
  return $token;
});

Request::hook('paging', function(Request $req, $cb = null){
  $query = $req->get();
  $condition = ['where'=>[]];
  if(isset($query[R_PAGENATOR_PAGE]) && is_numeric($query[R_PAGENATOR_PAGE])) {
    $condition[R_PAGENATOR_PAGE] = intval($query[R_PAGENATOR_PAGE]);
  }
  if(isset($query[R_PAGENATOR_LIMIT]) && is_numeric($query[R_PAGENATOR_LIMIT])) {
    $condition[R_PAGENATOR_LIMIT] = intval($query[R_PAGENATOR_LIMIT]);
  }
  if(!empty($query[R_ORDER])) {
    $condition[R_ORDER] = str_replace('-', ' ', $query[R_ORDER]);
  }
  // if(!empty($query[R_SEARCH])) {
  //   $condition['where'][R_SEARCH] = $query[R_SEARCH];
  // }
  if(!empty($cb)) {
    $condition = $cb($condition, $query);
  }
  return $condition;
});

Response::hook('return', function(Response $res, $result, $param = []) {
  $resp = [
    R_STATUS => empty($result) ? R_FAIL : R_SUCCESS,
    R_DATA => $result,
    R_CODE => 0,
    R_ERROR => '',
    R_STACK => ''
  ];
  $resp = _::assign($resp, $param);
  $res->data($resp);
});

Response::hook('success', function(Response $res){
  $res->data([
    R_STATUS => R_SUCCESS,
    R_DATA => null,
    R_CODE => 0,
    R_ERROR => '',
    R_STACK => ''
  ]);
});

Response::hook('fail', function(Response $res, $message='', $stack=[]){
  $return = [
    R_STATUS => R_FAIL,
    R_DATA => null,
    R_CODE => 0,
    R_ERROR => '',
    R_STACK => ''
  ];
  if($message !== '') {
    $return[R_ERROR] = $message;
  }
  if(!empty($stack)) {
    $return[R_STACK] = $stack;
  }
  $res->data($return);
});

Response::hook('error', function(Response $res, $e){
  $return = [
    R_STATUS => R_FAIL,
    R_ERROR => $e->getMessage(),
    R_STACK => [
      'message' => $e->getMessage()
    ]
  ];
  $res->data($return);
});

Response::hook('paging', function(Response $res, $result) {
  $content = [
    R_STATUS => R_SUCCESS,
    R_DATA => null,
    R_CODE => 0,
    R_ERROR => '',
    R_STACK => ''
  ];
  if($result!==null && 'object' === _::type($result)) {
    $content[R_DATA] = $result->items();
    $content[R_PAGENATOR] = [
      R_PAGENATOR_PAGE =>$result->currentPage(),
      R_PAGENATOR_PAGES =>$result->lastPage(),
      R_PAGENATOR_LIMIT =>$result->listRows(),
      R_PAGENATOR_COUNT =>$result->count(),
      R_PAGENATOR_TOTAL=>$result->total(),
    ];
    if($content[R_PAGENATOR][R_PAGENATOR_LIMIT]===0) {
      unset($content[R_PAGENATOR]);
    }
  } else {
    $content[R_DATA] = $result;
  }
  $res->data($content);
});

class CustomRoute {
  static $routes = [];
  static function scanner($opt = array()) {
    if(!isset($opt['recusive'])) {
      $opt['recusive'] = false;
    }
    if(isset($opt['dir']) && is_dir($opt['dir'])) {
      $dh = opendir($opt['dir']);
      while(($file=readdir($dh))!==false) {
        if($file !='.'&&$file!='..') {
          $fullpath = $opt['dir'].'/'.$file;
          if(is_file($fullpath)) {
            $arr = include($fullpath);
            foreach($arr as $k=>$v){
              self::$routes[$k] = $v;
            }
          }
          if(is_dir($fullpath)) {
            $opt2 = [
              'dir'=>$fullpath,
              'recusive' => $opt['recusive'],
              'callback' => isset($opt['callback']) ? $opt['callback'] : null
            ];
            self::scanner($opt2);
          }
        }
      }
    }
  }

  static function loadAll($opt) {
    self::scanner($opt);
    foreach(self::$routes as $k => $v) {
      $info = explode(' ', $k);
      $method = strtolower($info[0]);
      $route = $info[1];
      // TODO: match group
      if('pattern' == $method) {
        Route::pattern($route,$v);
      } else if(in_array($method, ['post', 'delete', 'put', 'get'])) {
        Route::rule($route.'$', function(Request $req, Response $res) use($v){
          $result = '';
          try {
            $result = $v($req, $res);
            if(empty($result)) {
              $result = $res->getData();
            }
            if(!is_string($result)) {
              $result = json($result);
            }
          } catch(Hinter $h) {
            $result = json($h->info);
          } catch(Exception $e) {
            $result = json([
              R_STATUS => R_FAIL,
              R_ERROR => $e->getMessage(),
              R_STACK => [
                'message' => $e->getMessage()
              ]
            ]);
          } catch(HttpException $he) {
            $result = json([
              R_STATUS => R_FAIL,
              R_ERROR => $he->getMessage(),
              R_STACK => [
                'message' => $he->getMessage()
              ]
            ]);
          }
          return $result;
        }, $method);
      }
    }
  }
}

?>