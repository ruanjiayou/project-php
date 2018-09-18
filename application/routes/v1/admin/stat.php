<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/stat 统计信息
   * @apiGroup admin-stat
   * @apiHeader {string} token
   * @apiSuccess
   * recharge: 累计充值数额
   */
  'get /v1/admin/stat' => function($req, $res) {
    $recharge = model('order')->where(['type'=>'recharge', 'status'=>'success'])->sum('price');
    $res->return([
      'recharge' => $recharge
    ]);
  },
  /**
   * @api {get} /v1/admin/search 查询
   * @apiGroup admin-search
   * @apiHeader {string} token
   * @apiParam {string} date,如: 2018-09-19
   */
  'get /v1/admin/search' => function($req, $res) {
    $query = input('get.');
    $data = model('invitation')
      ->where(['canceledAt'=>['between',[$query['date'].' 00:00:00',$query['date'].' 23:59:59']]])
      ->field('sellerPhone,sellerName,count(id) as times')
      ->group('sellerPhone')
      ->select();
    $res->paging($data);
  }
];