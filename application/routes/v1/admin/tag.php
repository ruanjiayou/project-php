<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/admin/tags/:adminId 标签
   * @apiGroup admin-tag
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} cataId 分类id
   * @apiParam {string} cataName 分类名称
   * @apiParam {string} name 标签名称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     name: 'y',
   *     cataId: 1,
   *     cataName: 'x',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->create(input('post.'));
    $res->return($result);
  },
  /**
   * @api {delete} /v1/admin/tags/:tagId 删除标签
   * @apiGroup admin-tag
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $tagBLL->destroy(input('delete.'));
    $res->success();
  },
  /**
   * @api {put} /v1/admin/tags/:tagId 修改标签
   * @apiGroup admin-tag
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} name 标签名称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     name: 'y',
   *     cataId: 1,
   *     cataName: 'x',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/tags/:tagId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->update(input('put.'), $req->param('tagId'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/tags 获取全部标签
   * @apiGroup admin-tag
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} cataId 分类id
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
  'get /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();
    $hql = ['where'=>[]];
    if(isset($_GET['cataId'])) {
      $hql['where'] = ['cataId'=>intval($_GET['cataId'])];
    }
    $result = $tagBLL->getAll($hql);
    $res->paging($result);
  },
  /**
   * @api {get} /v1/admin/tags/:tagId 获取标签详情
   * @apiGroup admin-tag
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     name: 'y',
   *     cataId: 1,
   *     cataName: 'x',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/tags/:tagId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->getInfo($req->param('tagId'));
    $res->return($result);
  }
  
];
?>