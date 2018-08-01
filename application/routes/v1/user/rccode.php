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
   *   status: 'success',
   *   result: {
   *     id: 1,
   *     phone: '18888888888',
   *     createdAt: "2018-07-31 17:43:48"
   *   }
   * }
   */
  'post /v1/user/rccode' => function($req, $res) {
    $user = UserBLL::auth($req);
    if($user['type']!=='agency') {
      thrower('user', 'userNotFound');
    }
    $result = RccodeBLL::create($user);
    $res->return($result);
  },
];

?>