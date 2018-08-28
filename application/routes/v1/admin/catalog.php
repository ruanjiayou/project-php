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
   * @apiParam {string} name 分类名称
   * @apiParam {string='user','seller','buyer'} [type='user'] 分类类型
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
   * @apiParam {string='user','seller','buyer'} [type='user'] 类型
   */
  'get /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    
    $catalogBLL =  new CatalogBLL();
    $hql = ['where'=>['type'=>isset($_GET['type'])?$_GET['type']:'user']];
    $result = $catalogBLL->getAll($hql);
    $res->paging($result);
  },
  /**
   * @api {get} /v1/admin/catalogs/:catalogId 分类详情
   * @apiGroup admin-catalog
   * 
   * @apiHeader {string} token 鉴权
   */
  'get /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    
    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->getInfo($req->param('catalogId'));
    $res->return($result);
  }
];
?>