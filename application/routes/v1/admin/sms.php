<?php
return [
  /**
   * @api {post} /v1/admin/sms-sign 添加短信签名
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {file} image 图片
   * @apiParam {string} title 签名名称
   * @apiParam {string} remark 申请备注
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     logicId: 1,
   *     title: '18888888888',
   *     content: 'max',
   *     type: 'sign',
   *     status: 'pending',
   *     reason: '',
   *     description: '',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/sms-sign' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $input = input('post.');
    if(isset($_FILES['image'])) {
      $filepath = ROOT_PATH.'public/images/'.$_FILES["image"]["name"];
      move_uploaded_file($_FILES["image"]["tmp_name"], $filepath);
      $input['image'] = _::file2base64($filepath);
      unlink($filepath);
    }
    $result = $smsBLL->addSign($input);
    $res->return($result);
  },
  'delete /v1/admin/sms-sign/:smsId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $smsBLL->delSign($req->param('smsId'));
    $res->success();
  },
  'put /v1/admin/sms-sign/:smsId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $result = $smsBLL->putSign($req->param('smsId'), input('put.'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/sms-sign 获取签名列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
   *     id: 1,
   *     logicId: 1,
   *     title: '18888888888',
   *     content: 'max',
   *     type: 'sign',
   *     status: 'pending',
   *     reason: '',
   *     description: '',
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
  'get /v1/admin/sms-sign' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $result = $smsBLL->getSign();
    $res->return($result);
  },
  /**
   * @api {post} /v1/admin/sms-tpl 获取模板列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} title 标题
   * @apiParam {string} text 内容
   * @apiParam {string} [remark] 备注
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     logicId: 1,
   *     title: '18888888888',
   *     content: 'max',
   *     type: 'common',
   *     status: 'pending',
   *     reason: '',
   *     description: '',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/sms-tpl' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $result = $smsBLL->addTpl(input('post.'));
    $res->return($result);
  },
  /**
   * @api {delete} /v1/admin/sms-tpl/:smsId 删除模板
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} title 标题
   * @apiParam {string} text 内容
   * @apiParam {string} [remark] 备注
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
  'delete /v1/admin/sms-tpl/:smsId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $smsBLL->delTpl($req->param('smsId'));
    $res->success();
  },
  /**
   * @api {get} /v1/admin/sms-tpl 获取模板列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量,做多50
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
   *     id: 1,
   *     logicId: 1,
   *     title: '18888888888',
   *     content: 'max',
   *     type: 'common',
   *     status: 'pending',
   *     reason: '',
   *     description: '',
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
  'get /v1/admin/sms-tpl' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $hql = $req->paging(function($h) {
      $h['where']['type'] = ['notin',['sign']];
      return $h;
    });
    $dataAndPaginator = $smsBLL->getTpl($hql);
    $res->return($dataAndPaginator['data'], [R_PAGENATOR=>$dataAndPaginator[R_PAGENATOR]]);
  },
  /**
   * @api {post} /v1/admin/sms-message 发送短信
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} phone 手机号
   * @apiParam {string='forgot','modify','zhuche','system','invite','cancel','refused','accepted','canceled'} type 类型
   * @apiParam {array} params 参数数组
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
    $smsBLL = new SmsBLL();

    $result = $smsBLL->sendMessage(input('post.'));
    $res->return($result);
  }
]
?>