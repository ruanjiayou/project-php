<?php
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/auth/admin/sign-in 登录
   * @apiGroup admin-authority
   * 
   * @apiParam {string} phone 手机号
   * @apiParam {string} password 密码
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success',
   *   result: {
   *     token: ''
   *   }
   * }
   */
  'post /v1/auth/admin/sign-in' => function($req, $res) {
    $result = AdminBLL::signIn(input('post.'));
    $res->return($result);
  },
  /**
   * @api {post} /v1/auth/admin/forgot-password 忘记密码
   * @apiGroup admin-authority
   * 
   * @apiParam {string} phone 手机号
   * @apiParam {string} password 密码
   * @apiParam {string} code 验证码
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success'
   * }
   */
  'post /v1/auth/admin/forgot-password' => function($req, $res) {
    //TODO:
    return 'admin-forgot-password';
  },
  /**
   * @api {post} /v1/auth/admin/reset-password 重置密码
   * @apiGroup admin-authority
   * 
   * @apiParam {string} phone 手机号
   * @apiParam {string} password 密码
   * @apiParam {string} code 验证码
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success'
   * }
   */
  'post /v1/auth/admin/reset-password' => function($req, $res) {
    //TODO:
    return 'admin-reset-password';
  }
];

?>