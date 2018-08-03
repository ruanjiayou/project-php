<?php
use app\model;
use think\Request;
use think\Response;

return [
  'get /v1/public/tags' => function($req, $res) {
    $tagBLL = new TagBLL();
    $res->paging($tagBLL->getAll());
  }
];
?>