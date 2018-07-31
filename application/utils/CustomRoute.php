<?php
use think\Route;
use think\Request;
use think\Response;

Request::hook('paging', function(Request $req, $cb = null){
  $query = $req->get();
  $condition = [];
  if(isset($query[R_PAGENATOR_PAGE]) && is_numeric($query[R_PAGENATOR_PAGE])) {
    $condition[R_PAGENATOR_PAGE] = intval($query[R_PAGENATOR_PAGE]);
  }
  if(isset($query[R_PAGENATOR_LIMIT]) && is_numeric($query[R_PAGENATOR_LIMIT])) {
    $condition[R_PAGENATOR_LIMIT] = intval($query[R_PAGENATOR_LIMIT]);
  }
  if(!empty($query[R_ORDER])) {
    $condition[R_ORDER] = str_replace('-', ' ', $query[R_ORDER]);
  }
  if(!empty($query[R_SEARCH])) {
    $condition[R_SEARCH] = $query[R_SEARCH];
  }
  if(!empty($cb)) {
    $condition = $cb($condition, $query);
  }
  return $condition;
});

Response::hook('return', function(Response $res, $result) {
  $res->data([
    R_STATUS => empty($result) ? R_SUCCESS : R_FAIL,
    R_DATA => $result,
    R_CODE => 0,
    R_ERROR => '',
    R_STACK => ''
  ]);
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
      'file' => $e['file'],
      'line' => $e['line'],
      'message' => $e['message'],
      'trace' => $e['trace'],
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
  //TODO: limit为0,没有分页
  if($result!==null && isset($result['listRows'])) {
    $content[R_DATA] = $result->listRows();
    $content[R_PAGENATOR] = [
      R_PAGENATOR_PAGE =>$result->currentPage(),
      R_PAGENATOR_PAGES =>$result->lastPage(),
      R_PAGENATOR_LIMIT =>$result->listRows(),
      R_PAGENATOR_COUNT =>$result->count(),
      R_PAGENATOR_TOTAL=>$result->total(),
    ];
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
        Route::rule($route, function(Request $req, Response $res) use($v){
          try {
            $result = $v($req, $res);
            if(empty($result)) {
              $result = $res->getData();
            }
            if(is_string($result)) {
              return $result;
            } else {
              return json($result);
            }
          } catch(Hinter $h) {
            return $h->info;
          } catch(Exception $e) {
            $res->error($e);
          } catch(HttpException $he) {
            $res->error($he);
          }
        }, $method);
      }
    }
  }
}

?>