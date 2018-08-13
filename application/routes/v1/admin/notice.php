<?php
return [
  /**
   * @api {post} /v1/admin/notices 添加系统消息(公告)
   * @apiGroup admin-notices
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} title 公告标题
   * @apiParam {string} content 公告内容
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     smsId: 1,
   *     title: '18888888888',
   *     content: '18888888888',
   *     phone: '18888888888',
   *     json: '[]',
   *     type: 'system',
   *     status: 'success',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/notices' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $smsMessageBLL = new SmsMessageBLL();
    $admin = $adminBLL::auth($req);

    $result = $smsMessageBLL->create(input('post.'));
    $res->return($result);
  },
  /**
   * @api {delete} /v1/admin/notices/:noticeId 删除系统消息(公告)
   * @apiGroup admin-notices
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/admin/notices/:noticeId' => function($req, $res) {
    $smsMessageBLL = new SmsMessageBLL();
    $admin = AdminBLL::auth($req);

    $smsMessageBLL->destroy($req->param('noticeId'));
    $res->success();
  },
  /**
   * @api {put} /v1/admin/notices/:noticeId 修改系统消息(公告)
   * @apiGroup admin-notices
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} title 公告标题
   * @apiParam {string} content 公告内容
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     smsId: 1,
   *     title: '18888888888',
   *     content: '18888888888',
   *     phone: '18888888888',
   *     json: '[]',
   *     type: 'system',
   *     status: 'success',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/notices/:noticeId' => function($req, $res) {
    $smsMessageBLL = new SmsMessageBLL();
    $admin = AdminBLL::auth($req);

    $result = $smsMessageBLL->update(input('put.'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/notices/:noticeId 系统消息(公告)列表
   * @apiGroup admin-notices
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
   *   ecode: 0,
   *   error: '',
   *   stack: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 10,
   *     count: 1,
   *     total: 1,
   *   }
   * }
   */
  'get /v1/admin/notices' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $smsMessageBLL = new SmsMessageBLL();
    $admin = $adminBLL::auth($req);

    $hql = $req->paging(function($h){
      $h['where']['type'] = 'system';
      return $h;
    });
    $result = $smsMessageBLL->getList($hql);
    $res->paging($result);
  },
  /**
   * @api {get} /v1/admin/notices/:noticeId 系统消息(公告)详情
   * @apiGroup admin-notices
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     smsId: 1,
   *     title: '18888888888',
   *     content: '18888888888',
   *     phone: '18888888888',
   *     json: '[]',
   *     type: 'system',
   *     status: 'success',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/admin/notices/:noticeId' => function($req, $res) {
    $smsMessageBLL = new SmsMessageBLL();
    $admin = AdminBLL::auth($req);

    $result = $smsMessageBLL->getInfo(['id'=>$req->param('noticeId')]);
    $res->return($result);
  }
];

?>