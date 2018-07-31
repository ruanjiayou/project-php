<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {put} /v1/user/self 修改个人资料
   * @apiGroup user-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} trueName 真实姓名
   * @apiParam {string} nickName 昵称
   * @apiParam {int} age 密码
   * @apiParam {int} height 验证码
   * @apiParam {int} weight 验证码
   * @apiParam {string} city 所在城市
   * @apiParam {string} identity 身份证
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success',
   *   result: {
   *     id: 1,
   *   }
   * }
   */
  'put /v1/user/self' => function($req, $res) {
    //TODO:
    return 'user-self';
  },
  /**
   * @api {put} /v1/user/password 修改密码
   * @apiGroup user-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} password 新密码
   * @apiParam {string} code 验证码
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success',
   *   result: {
   *     id: 1,
   *   }
   * }
   */
  'put /v1/user/password' => function($req, $res) {
    //TODO:
    return 'user-self';
  },
  /**
   * @api {get} /v1/user/self 获取个人资料
   * @apiGroup user-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success',
   *   result: {
   *     id: 1,
   *   }
   * }
   */
  'get /v1/user/self' => function($req, $res) {
    $user = UserBLL::auth($req);
    $res->return($user);
  }
];

?>