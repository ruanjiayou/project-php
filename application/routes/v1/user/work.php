<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/user/works 设置工作日
   * @apiGroup user-work
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {array} works 数组,如['2018-08-08']
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     userId: 1,
   *     workAt: '2018-08-05 00:00:00',
   *     createdAt: '2018-08-05 01:02:37',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/user/works' => function($req, $res) {
    $user = UserBLL::auth($req);
    $userWorkBLL = new UserWorkBLL();

    $results = $userWorkBLL->setWorks(input('post.'), $user['id']);
    $res->paging($results);
  },
  /**
   * @api {get} /v1/user/works 某月工作日列表
   * @apiGroup user-work
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [year] 年份
   * @apiParam {int} [month] 月份
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     userId: 1,
   *     workAt: '2018-08-05 00:00:00',
   *     createdAt: '2018-08-05 01:02:37',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/user/works' => function($req, $res) {
    $user = UserBLL::auth($req);
    $userWorkBLL = new UserWorkBLL();
    
    $results = $userWorkBLL->getMonthWorks($user['id'], input('get.'));
    $res->paging($results);
  },
  /**
   * @api {delete} /v1/user/works/:workAt 取消工作日
   * @apiGroup user-work
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} workAt 如: '2018-08-08'
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     userId: 1,
   *     workAt: '2018-08-05 00:00:00',
   *     createdAt: '2018-08-05 01:02:37',
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/user/works/:workAt' => function($req, $res) {
    $user = UserBLL::auth($req);
    $userWorkBLL = new UserWorkBLL();
    if($userWorkBLL->destroy($req->param('workAt'))) {
      $res->success();
    } else {
      $res->fail();
    }
  }
];

?>