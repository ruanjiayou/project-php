<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/admin/catalogs 添加分类
   * @apiGroup admin-catalog
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} [name] 分类名称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     name: '18888888888',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);

    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->create(input('post.'));
    $res->return($result);
  },
  /**
   * @api {delete} /v1/admin/catalogs 添加分类
   * @apiGroup admin-catalog
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {array} id id数组,body参数
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
  'delete /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);

    $catalogBLL =  new CatalogBLL();
    $catalogBLL->destroy(input('delete.'));
    $res->success();
  },
  /**
   * @api {put} /v1/admin/catalogs/:catalogId 修改分类
   * @apiGroup admin-catalog
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} name 分类名称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     name: '18888888888',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $admin = AdminBLL::auth($req);

    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->update(input('put.'), $req->param('catalogId'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/catalogs 获取全部分类
   * @apiGroup admin-catalog
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} name 分类名称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
   *     id: 1,
   *     name: '18888888888',
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
  'get /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    
    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->getAll();
    $res->paging($result);
  },
  /**
   * @api {get} /v1/admin/catalogs/:catalogId 分类详情
   * @apiGroup admin-catalog
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     name: '18888888888',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    
    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->getInfo($req->param('catalogId'));
    $res->return($result);
  }
];
?>