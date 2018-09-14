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
   * @apiParam {int=0,1} read 已读/未读
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
      $h['where'] = ['type'=> 'system','phone'=>$user['phone'],'isDeleted'=>0];
      if(isset($_GET['read'])) {
        $h['where']['read'] = $_GET['read'] == '1' ? 1 : 0;
      }
      return $h;
    });
    $result = $smsMessageBLL->getList($hql);
    $res->paging($result);
  },
  /**
   * @api {post} /v1/user/message/:messageId 修改消息
   * @apiGroup user-messages
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int=1} [isRead] 已读
   * @apiParam {int=1} [isDeleted] 已删除
   */
  'post /v1/user/message/:messageId' => function($req, $res) {
    $user = UserBLL::auth($req);
    $smsMessageBLL = new SmsMessageBLL();
    $input = input('post.');
    $data = [];
    if(isset($input['isRead'])) {
      $data['isRead'] = 1;
    }
    if(isset($input['isDeleted'])) {
      $data['isDeleted'] = 1;
    }
    $smsMessageBLL->update($data, ['id'=>$req->param('messageId')]);
    $res->success();
  }
];
?>