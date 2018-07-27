<?php
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /test
   * @apiName test
   * @apiGroup test
   * @apiDescription 访问地址/test?page=1&limit=2&order=id-DESC&search=搜索,返回paging()处理后的json
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
  'get /test' => function(Request $req, Response $res) {
    header('Content-Type:application/json; charset=utf8');
    $result = $req->paging();
    return $res->return($result);
  }
]
?>