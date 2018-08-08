<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/users 用户列表
   * @apiGroup admin-user
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量默认10
   * @apiParam {string} [search] 手机号或昵称
   * @apiParam {string='servant','buyer','agency'} [type] 用户类型
   * @apiParam {string='approved','approving','forbidden','registered'} [status] 用户状态
   * @apiParam {string='hot','recommend','normal'} [attr] 属性
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   ecode: 0,
   *   error: '',
   *   rdata: [{
   *     id: 1,
   *     phone: '18888888888',
   *     identity: '421224199311111111',
   *     rccode: '123456',
   *     trueName: '阮家友',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     introduce: '简介',
   *     tags: '',
   *     height: 160,
   *     weight: 100,
   *     score: 4.9,
   *     x: "0.0000",
   *     y: "0.0000",
   *     images: 6,
   *     popular: 10086,
   *     money: 888,
   *     address: "",
   *     city: '武汉',
   *     type: "servant",
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
   *   statck: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 10,
   *     count: 1,
   *     total: 1,
   *   }
   * }
   */
  'get /v1/admin/users' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();
    
    $hql = $req->paging(function($h){
      $h['where'] = input('get.');
      return $h;
    });
    $res->paging($userBLL->getList($hql));
  },
  /**
   * @api {get} /v1/admin/users/:userId 用户详情
   * @apiGroup admin-user
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   ecode: 0,
   *   error: '',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     identity: '421224199311111111',
   *     rccode: '123456',
   *     trueName: '阮家友',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     introduce: '简介',
   *     tags: '',
   *     height: 160,
   *     weight: 100,
   *     score: 4.9,
   *     x: "0.0000",
   *     y: "0.0000",
   *     images: 6,
   *     popular: 10086,
   *     money: 888,
   *     address: "",
   *     city: '武汉',
   *     type: "servant",
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   statck: ''
   * }
   */
  'get /v1/admin/users/:userId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();

    $user = $userBLL->getInfo($req->param('userId'));
    $res->return($user);
  },
  /**
   * @api {get} /v1/admin/users/:userId 修改用户
   * @apiGroup admin-user
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string='hot','recommend'} [attr] 设置属性
   * @apiParam {string='approved','forbidden'} [status] 审核
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   ecode: 0,
   *   error: '',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     identity: '421224199311111111',
   *     rccode: '123456',
   *     trueName: '阮家友',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     introduce: '简介',
   *     tags: '',
   *     height: 160,
   *     weight: 100,
   *     score: 4.9,
   *     x: "0.0000",
   *     y: "0.0000",
   *     images: 6,
   *     popular: 10086,
   *     money: 888,
   *     address: "",
   *     city: '武汉',
   *     type: "servant",
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   statck: ''
   */
  'put /v1/admin/users/:userId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();

    $user = $userBLL->update(_::pick(input('put.'), ['status', 'attr']), ['id'=>$req->param('userId')]);
    $res->return($user);
  }
];
?>