<?php
use think\Request;
use think\Response;

return [
  'get /test' => function($req, $res) {
    return 'Hello World!';
  },
  'get /test1' => function($req, $res) {
    return json(['name'=>'test']);
  },
  /**
   * @api {get} /test/req
   * @apiName test
   * @apiGroup test
   * @apiDescription 访问地址/test/req?page=1&limit=2&order=id-DESC&search=搜索,返回paging()处理后的json
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   page: 1,
   *   limit: 2,
   *   order: 'id DESC',
   *   search: '搜索'
   * }
   */
  'get /test/req/paging' => function($req, $res) {
    $result = $req->paging();
    $res->return($req->paging());
  },
  'get /test/res/return' => function($req, $res) {
    $res->return(['test']);
  },
  'get /test/res/success' => function($req, $res) {
    $res->success();
  },
  'get /test/res/fail' => function($req, $res) {
    $res->fail();
  },
  'get /test/res/paging' => function($req, $res) {
    $res->paging(['test']);
  },
]
?>