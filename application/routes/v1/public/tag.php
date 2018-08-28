<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/tags 获取全部标签
   * @apiGroup public-tag
   * 
   * @apiParam {string='user','seller','buyer'} [type='user'] 标签类型
   */
  'get /v1/public/tags' => function($req, $res) {
    $tagBLL = new TagBLL();
    $catalogBLL = new CatalogBLL();
    $type = isset($_GET['type']) ? $_GET['type']: 'user';
    $query = ['where'=>['type'=>$type]];
    $tags = $tagBLL->getAll($query);
    $catas = $catalogBLL->getAll($query);
    $res->return($tags, ['catalog'=>$catas]);
  }
];
?>