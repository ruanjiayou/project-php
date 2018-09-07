<?php
return [
  /**
   * @api {get} /v1/user/messages 消息列表
   * @apiGroup user-messages
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量默认10
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   ecode: 0,
   *   error: '',
   *   rdata: [{
   *     id: 1,
   *     smsId: 1,
   *     title: '18888888888',
   *     content: '18888888888',
   *     phone: '18888888888',
   *     json: '[]',
   *     type: 'system',
   *     status: 'success',
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
  'get /v1/user/messages' => function($req, $res) {
    $user = UserBLL::auth($req);
    $smsMessageBLL = new SmsMessageBLL();

    $hql = $req->paging(function($h) use($user){
      $h['whereOr'] = ['type'=> 'system','phone'=>$user['phone']];
      return $h;
    });
    $result = $smsMessageBLL->getList($hql);
    $res->paging($result);
  }
];
?>