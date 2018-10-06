<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/user/rccode 生成推荐码
   * @apiGroup user-rccode
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     agencyId: 1,
   *     agencyName: 'max',
   *     agencyAvatar: '',
   *     rccode: '',
   *     type: 'pending',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/user/rccode' => function($req, $res) {
    $userBLL = new UserBLL();
    $rccodeBLL = new RccodeBLL();
    $user = $userBLL->auth($req);

    if($user['type']!=='agency') {
      thrower('user', 'userNotFound');
    }
    $result = $rccodeBLL->create($user);
    $res->return($result);
  },
  /**
   * @api {delete} /v1/user/rccode/:userId 中介删除合作关系,暂时不允许此项操作
   * @apiGroup user-rccode
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'fail',
   *   edata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/user/rccode/:userId' => function($req, $res) {
    $userBLL = new UserBLL();
    $rccodeBLL = new RccodeBLL();
    $user = $userBLL->auth($req);

    $result = [];
    $res->fail();
  },
  /**
   * @api {get} /v1/user/rccode 获取合作人列表
   * @apiGroup user-rccode
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: [{
   *     id: 1,
   *     agencyId: 1,
   *     agencyName: 'max',
   *     agencyAvatar: '',
   *     rccode: '',
   *     type: 'pending',
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
   *   ecode: 0,
   *   error: '',
   *   stack: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 10,
   *     count: 1,
   *     total: 1
   *   }
   * }
   */
  'get /v1/user/rccode' => function($req, $res) {
    $userBLL = new UserBLL();
    $rccodeBLL = new RccodeBLL();
    $user = $userBLL->auth($req);

    $hql = $req->paging(function($h) use($user) {
      if($user['type']==='agency') {
        $h['where']['agencyId'] = $user['id'];
        $h['where']['userId'] = ['NEQ','NULL'];
      } else {
        $h['where']['userId'] = $user['id'];
      }
      return $h;
    });
    $users = $rccodeBLL->getAll($hql);
    $res->paging($users);
  }
];

?>