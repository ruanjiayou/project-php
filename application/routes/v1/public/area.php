<?php
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/areas 区域列表
   * @apiGroup public-area
   * 
   * @apiParam {int} [pid=0] 父id,0代表获取所有省
   */
  'get /v1/public/areas' => function($req, $res) {
    $query = ['limit'=>0, 'where'=>[], 'order'=>'id ASC'];
    $query['where']['pid'] = isset($_GET['pid']) ? $_GET['pid'] : 0;
    $result = model('area')->getList($query);
    $res->paging($result);
  }
];

?>