<?php
return [
  /**
   * @api {get} /v1/admin/price/signin 获取签到奖励
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     value: 10,
   *     type: 'signin',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/price/signin' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $res->return(PriceBLL::getSignin());
  },
  /**
   * @api {put} /v1/admin/price/signin 修改签到奖励
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} value 价格
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     value: 10,
   *     type: 'signin',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/price/signin' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $res->return(PriceBLL::putSignin(input('put.')));
  },
  /**
   * @api {post} /v1/admin/price/orders 添加订单定价
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} value 价格
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     value: 10,
   *     type: 'order',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/price/orders' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = PriceBLL::screate(input('post.'));
    $res->return($result);
  },
  /**
   * @api {delete} /v1/admin/price/orders/:orderId 删除定价
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/admin/price/orders/:orderId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $priceBLL = new PriceBLL();
    $priceBLL->destroy($req->param('orderId'));
    $res->success();
  },
  /**
   * @api {put} /v1/admin/price/orders/:orderId 修改订单价格
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} value 价格
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     value: 10,
   *     type: 'order',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/price/orders/:orderId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = PriceBLL::putOrder(input('put.'), $req->param('orderId'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/price/orders 获取订单价格列表
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: [{
   *     id: 1,
   *     value: 10,
   *     type: 'order',
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
   *   ecode: 0,
   *   error: '',
   *   stack: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 10,
   *     count: 1,
   *     total: 1
   *   }
   * }
   */
  'get /v1/admin/price/orders' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $res->paging(PriceBLL::getOrders());
  },
  /**
   * @api {get} /v1/admin/price/orders/:orderId 获取订单价格详情
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     value: 10,
   *     type: 'order',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/price/orders/:orderId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $priceBLL = new PriceBLL();
    $result = $priceBLL->getInfo($req->param('orderId'));
    $res->return($result);
  },
];
?>