<?php
use app\model;
use think\Request;
use think\Response;

return [
  'post /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);

    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->create(input('post.'));
    $res->return($result);
  },
  'delete /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);

    $catalogBLL =  new CatalogBLL();
    $catalogBLL->destroy(input('delete.'));
    $res->success();
  },
  'put /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $admin = AdminBLL::auth($req);

    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->update(input('put.'), $req->param('catalogId'));
    $res->return($result);
  },
  'get /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    
    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->getAll();
    $res->paging($result);
  },
  'get /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    
    $catalogBLL =  new CatalogBLL();
    $result = $catalogBLL->getInfo($req->param('catalogId'));
    $res->return($result);
  }
];
?>