<?php
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/auth/user/sign-up 注册
   * @apiGroup user-authority
   * @apiDescription 业务逻辑:
   * .servant类型,必须填rccode
   * .查找手机号是否已注册
   * .推荐码是否存在,是否被使用
   * .验证码是否存在,是否过期
   * 
   * @apiParam {string='buyer','servant','agency'} type 用户类型
   * @apiParam {string} phone 手机号
   * @apiParam {string} password 密码
   * @apiParam {string} nickName 昵称
   * @apiParam {string} code 验证码
   * @apiParam {string} [rccode] 推荐码,type=servant时必填
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success'
   * }
   */
  'post /v1/auth/user/sign-up' => function($req, $res) {
    $result = UserBLL::signUp(input('post.'));
    $res->return($result);
  },
  /**
   * @api {post} /v1/auth/user/sign-in 登录
   * @apiGroup user-authority
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
  'post /v1/auth/user/sign-in' => function($req, $res) {
    $result = UserBLL::signIn(input('post.'));
    $res->return($result);
  },
  /**
   * @api {post} /v1/auth/user/forgot-password 忘记密码
   * @apiGroup user-authority
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
  'post /v1/auth/user/forgot-password' => function($req, $res) {
    //TODO:
    return 'user-forgot-password';
  },
  /**
   * @api {post} /v1/auth/user/reset-password 重置密码
   * @apiGroup user-authority
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
  'post /v1/auth/user/reset-password' => function($req, $res) {
    //TODO:
    return 'user-reset-password';
  }
];

?>