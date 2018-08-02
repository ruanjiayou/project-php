<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {put} /v1/admin/self 修改个人资料
   * @apiGroup admin-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} [nickName] 昵称
   * @apiParam {string} [avatar] 头像
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success',
   *   result: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 1,
   *     createdAt: "2018-07-31 17:43:48"
   *   }
   * }
   */
  'put /v1/admin/self' => function($req, $res) {
    $user = AdminBLL::auth($req);
    $result = AdminBLL::update(input('put.'), ['id'=>$user['id']]);
    $res->return(_::filter($result, ['password', 'token', 'salt']));
  },
  /**
   * @api {put} /v1/admin/password 修改密码
   * @apiGroup admin-self
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
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 1,
   *     createdAt: "2018-07-31 17:43:48"
   *   }
   * }
   */
  'put /v1/admin/password' => function($req, $res) {
    //TODO:
    return 'admin-self';
  },
  /**
   * @api {get} /v1/admin/self 获取个人资料
   * @apiGroup admin-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success',
   *   result: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 1,
   *     createdAt: "2018-07-31 17:43:48"
   *   }
   * }
   */
  'get /v1/admin/self' => function($req, $res) {
    $user = AdminBLL::auth($req);
    $res->return(_::filter($user, ['password', 'token', 'salt']));
  }
];

?>