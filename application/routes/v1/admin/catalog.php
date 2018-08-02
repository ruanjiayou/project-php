<?php
use app\model;
use think\Request;
use think\Response;

return [
  'post /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = CatalogBLL::create(input('post.'));
    $res->return($result);
  },
  'delete /v1/admin/catalogs' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    CatalogBLL::destroy(input('delete.'));
    $res->success();
  },
  'put /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = CatalogBLL::update(input('put.'), $req->param('catalogId'));
    $res->return($result);
  },
  'get /v1/admin/catalogs' => function($req, $res) {
    $result = CatalogBLL::getList();
    $res->paging($result);
  },
  'get /v1/admin/catalogs/:catalogId' => function($req, $res) {
    $result = CatalogBLL::getInfo($req->param('catalogId'));
    $res->return($result);
  }
];
?>