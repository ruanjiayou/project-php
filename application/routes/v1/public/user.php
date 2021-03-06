<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/users 获取用户列表
   * @apiGroup public-user
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
   *   ecode: 0,
   *   error: '',
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
  'get /v1/public/users' => function($req, $res) {
    $userBLL = new UserBLL();

    $hql = $req->paging(function($h) {
      $h['where'] = input('get.');
      $h['where']['type'] = 'servant';
      $h['where']['status'] = 'approved';
      return $h;
    });
    $res->paging($userBLL->getList($hql));
  },
  'get /v1/public/users/:userId/works' => function($req, $res) {
    $userWorkBLL = new UserWorkBLL();

    $results = $userWorkBLL->getMonthWorks($req->param('userId'), input('get.'));
    $res->paging($results);
  }
];
?>