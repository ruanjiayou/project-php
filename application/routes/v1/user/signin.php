<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/user/sign-in 进行签到
   * @apiGroup user-signin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/user/sign-in' => function($req, $res) {
    $user = UserBLL::auth($req);
    if(null !== SigninBLL::signin($user)) {
      $res->success();
    } else {
      $res->fail();
    }
  },
  /**
   * @api {get} /v1/user/sign-in 某月签到记录列表
   * @apiGroup user-signin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
   *     id: 1,
   *     userId: 1,
   *     createdAt: '2018-08-04 00:00:00'
   *   }],
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/user/sign-in' => function($req, $res) {
    $user = UserBLL::auth($req);
    $signs = SigninBLL::monthSigns($user['id'], input('get.'));
    $res->paging($signs);
  },
  /**
   * @api {get} /v1/user/signed 今天是否签到
   * @apiGroup user-signin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/user/signed' => function($req, $res) {
    $user = UserBLL::auth($req);
    if(SigninBLL::signed($user['id'])) {
      $res->success();
    } else {
      $res->fail();
    }
  }
];