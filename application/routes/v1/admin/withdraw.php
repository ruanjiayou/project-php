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
   * @apiParam {string='pending','success','fail'} [status] 状态
   * @apiParam {string} [search] 手机号
   * @param User 合伙人详情
   */
  'get /v1/admin/withdraw' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $orderBLL = new OrderBLL();

    $hql = $req->paging(function($q) {
      $q['where'] = ['type'=>'withdraw'];
      if(isset($_GET['status'])) {
        $q['where']['status'] = $_GET['status'];
      }
      return $q;
    });
    if(isset($hql['search']) && $hql['search']!=='') {
      $hql['where']['phone'] = ['like', '%'.$hql['search'].'%'];
    }
    $hql['scopes'] = ['User'];
    $result = $orderBLL->getList($hql);
    $res->paging($result);
  },
  /**
   * @api {get} /v1/admin/withdraw/:withdrawId 提现详情
   * @apiGroup admin-wallet
   * @apiHeader {string} token 鉴权
   */
  'get /v1/admin/withdraw/:withdrawId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $orderBLL = new OrderBLL();
    $order = $orderBLL->getInfo($req->param('withdrawId'));
    if(null !== $order) {
      // TODO: _::filter
      $order['user'] = (new UserBLL())->getInfo($order['userId']);
    }
    $res->return($order);
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