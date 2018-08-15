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
    $aop = new AopClient ();
    dump($aop);
    $res->return([]);
  },
  /**
   * @api {post} /v1/user/wallet/withdraw 提现
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
  'post /v1/user/wallet/withdraw' => function($req, $res) {

  }
];
?>