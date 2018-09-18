<?php
return [
  /**
   * @api {post} /v1/user/wallet/recharge 充值
   * @apiGroup user-wallet 
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} money 金额
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
  'post /v1/user/wallet/recharge' => function($req, $res) {
    $user = UserBLL::auth($req);
    $orderBLL = new OrderBLL();
    
    $order = $orderBLL->create(['type'=>'recharge', 'phone'=>$user['phone'], 'userId'=>$user['id'], 'price'=>input('post.money'),]);
    $res->return($order);
  },
  /**
   * @api {post} /v1/user/wallet/withdraw 提现
   * @apiGroup user-wallet 
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} money 金额
   * @apiParam {int} type 类型,银行卡或支付宝(creditCard/alipay)
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
  'post /v1/user/wallet/withdraw' => function($req, $res) {
    $user = UserBLL::auth($req);
    $orderBLL = new OrderBLL();
    $input = input('post.');
    if($input['money'] > $user['money']) {
      thrower('order', 'moneyLess');
    }
    $smsMesageBLL = new SmsMessageBLL();
    $order = $orderBLL->create(['type'=>'withdraw', 'phone'=>$user['phone'], 'userId'=>$user['id'], 'price'=>$input['money'], 'origin'=> isset($input['type'])? $input['type']:'']);
    $smsMesageBLL->sendMessage([
      'phone' => $user['phone'],
      'type' => 'withdraw',
      'cid' => $user['cid'],
      'params' => [$user['nickName'], date('Y-m-d H:i:s')]
    ]);
    $res->return($order);
  },
  /**
   * @api {get} /v1/user/wallet/yestoday 昨日收益
   * @apiGroup user-wallet
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
  'get /v1/user/wallet/yestoday' => function($req, $res) {
    $user = UserBLL::auth($req);

    //获取今天00:00
    $start = strtotime(date('Y-m-d'.'00:00:00',time()-3600*24));
    //获取今天24:00
    $end = strtotime(date('Y-m-d'.'00:00:00',time()));
    $money = model('user_bill')->where(['userId'=>$user['id'], 'type'=>'income', 'createdAt'=>['between', [$start, $end]]])->sum('value');
    $res->return(['money'=>$money]);
  }
];
?>