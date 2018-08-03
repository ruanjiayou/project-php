<?php
use app\model;
use think\Request;
use think\Response;

return [
  'post /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->create(input('post.'));
    $res->return($result);
  },
  'delete /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $tagBLL->destroy(input('delete.'));
    $res->success();
  },
  'put /v1/admin/tags/:tagId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->update(input('put.'), $req->param('tagId'));
    $res->return($result);
  },
  'get /v1/admin/tags' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->getAll();
    $res->paging($result);
  },
  'get /v1/admin/tags/:tagId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $tagBLL = new TagBLL();

    $result = $tagBLL->getInfo($req->param('tagId'));
    $res->return($result);
  }
  
];
?>