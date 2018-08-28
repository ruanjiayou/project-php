<?php
return [
  /**
   * @api {post} /v1/admin/sms-message 发送短信
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} [phone] 手机号
   * @apiParam {string='forgot','modify','zhuche','system','invite','cancel','refused','accepted','canceled'} type 类型
   * @apiParam {array} params 参数数组
   * @apiParam {string} [title] type为system时的标题
   * @apiParam {string} [content] type为system时的内容
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     "result": 0,
   *     "errmsg": "OK",
   *     "ext": "",
   *     "sid": "8:tk5JWxVVc33vJCsQMBz20180808",
   *     "fee": 1
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/sms-message' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsMessageBLL = new SmsMessageBLL();

    $result = $smsMessageBLL->sendMessage(input('post.'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/sms-message 信息列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量
   * @apiParam {string='forgot','modify','zhuche','system','invite','cancel','refused','accepted','canceled'} [type] 类型
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
   *     "result": 0,
   *     "errmsg": "OK",
   *     "ext": "",
   *     "sid": "8:tk5JWxVVc33vJCsQMBz20180808",
   *     "fee": 1
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }]
   */
  'get /v1/admin/sms-message' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsMessageBLL = new SmsMessageBLL();

    $hql = $req->paging(function($query){
      if(isset($_GET['type']) && $_GET['type']!=='') {
        $query['where']['type'] = $_GET['type'];
      }
      return $query;
    });
    $result = $smsMessageBLL->getList($hql);
    $res->paging($result);
  }
];
?>