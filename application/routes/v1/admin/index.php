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
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 1,
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/self' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $admin = $adminBLL::auth($req);

    $result = $adminBLL->update(input('put.'), $admin['id']);
    $res->return(_::filter($result, ['password', 'token', 'salt']));
  },
  /**
   * @api {put} /v1/admin/password 修改密码
   * @apiGroup admin-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} oldpsw 旧密码
   * @apiParam {string} newpsw 新密码
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 1,
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/password' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $adminBLL = new AdminBLL();
    $adminBLL->changePassword($admin, input('put.oldpsw'), input('put.newpsw'));
    $res->success();
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
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 1,
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/self' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $res->return(_::filter($admin, ['password', 'token', 'salt']));
  }
];

?>