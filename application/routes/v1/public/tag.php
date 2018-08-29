<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/tags 获取全部标签
   * @apiGroup public-tag
   * 
   * @apiParam {int} [cataId] 分类id
   * @apiParam {string='user','seller','buyer'} [type='user'] 标签类型
   */
  'get /v1/public/tags' => function($req, $res) {
    $tagBLL = new TagBLL();
    $catalogBLL = new CatalogBLL();
    $hql = ['where'=>['type'=>isset($_GET['type'])?$_GET['type']:'user']];
    if(isset($_GET['cataId'])) {
      $hql['where']['cataId'] = $_GET['cataId'];
    }
    $tags = $tagBLL->getAll($hql);
    unset($hql['where']['cataId']);
    $catas = $catalogBLL->getAll($hql);
    $res->return($tags, ['catalog'=>$catas]);
  }
];
?>