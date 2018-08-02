<?php
use app\model;
use think\Request;
use think\Response;

return [
  'post /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = TagBLL::create(input('post.'));
    $res->return($result);
  },
  'delete /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    TagBLL::destroy(input('delete.'));
    $res->success();
  },
  'put /v1/admin/tags/:tagId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = TagBLL::update(input('put.'), $req->param('tagId'));
    $res->return($result);
  },
  'get /v1/admin/tags' => function($req, $res) {
    $result = TagBLL::getList();
    $res->paging($result);
  },
  'get /v1/admin/tags/:tagId' => function($req, $res) {
    $result = TagBLL::getInfo($req->param('tagId'));
    $res->return($result);
  }
];
?>