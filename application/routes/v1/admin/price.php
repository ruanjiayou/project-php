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
   * @api {get} /v1/admin/price/rebate 获取用户分成比例,与中介的和固定为80.
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   */
  'get /v1/admin/price/rebate' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $res->return(PriceBLL::getRebate());
  },
  /**
   * @api {put} /v1/admin/price/rebate 修改户分成比例,与中介的和固定为80.
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} value 比例
   */
  'put /v1/admin/price/rebate' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $res->return(PriceBLL::putRebate(input('put.')));
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
   * @api {put} /v1/admin/price/orders/:orderId 修改单个订单价格
   * @apiName single-price
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} value 价格
   */
  'put /v1/admin/price/orders/:orderId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $result = PriceBLL::putOrder(input('put.'), $req->param('orderId'));
    $res->return($result);
  },
  /**
   * @api {put} /v1/admin/price/orders 修改多个订单价格
   * @apiName multi-price
   * @apiGroup admin-price
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {array} prices 价格数组
   * @apiParam {int} prices.id 邀请定价id
   * @apiParam {int} prices.value 邀请定价
   */
  'put /v1/admin/price/orders' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $prices = input('put.')['prices'];
    for($i=0;$i<count($prices);$i++) {
      PriceBLL::putOrder(['value'=>$prices[$i]['value']],$prices[$i]['id']);
    }
    $res->success();
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