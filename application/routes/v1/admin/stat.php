<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/stat 统计信息
   * @apiHeader {string} token
   */
  'get /v1/admin/stat' => function($req, $res) {
    $recharge = model('order')->where(['type'=>'recharge', 'status'=>'success'])->sum('price');
    $res->return([
      'recharge' => $recharge
    ]);
  }
];