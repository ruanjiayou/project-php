<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/withdraw 提现列表
   * @apiGroup admin-wallet
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/withdraw' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $orderBLL = new OrderBLL();

    $hql = $req->paging(function($q) {
      $q['where'] = ['type'=>'withdraw'];
      return $q;
    });
    $result = $orderBLL->getList($hql);
    $res->paging($result);
  },
  /**
   * @api {put} /v1/admin/withdraw/:orderId 安排提现
   * @apiGroup admin-wallet
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/withdraw/:orderId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $orderBLL = new OrderBLL();
    $orderBLL->withdraw($req->param('orderId'));
    $res->success();
  }
];

?>