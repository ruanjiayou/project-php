<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/tags 获取全部标签
   * @apiGroup public-tag
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
   *     id: 1,
   *     name: 'y',
   *     cataId: 1,
   *     cataName: 'x',
   *   }],
   *   ecode: 0,
   *   error: '',
   *   stack: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 0,
   *     count: 1,
   *     total: 1,
   *   }
   * }
   */
  'get /v1/public/tags' => function($req, $res) {
    $tagBLL = new TagBLL();
    $res->paging($tagBLL->getAll());
  }
];
?>