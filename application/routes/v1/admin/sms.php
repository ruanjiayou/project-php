<?php
return [
  /**
   * @api {post} /v1/admin/sms-sign 添加短信签名
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {file} image 图片
   * @apiParam {string} text 签名名称
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
  // 'post /v1/admin/sms-sign' => function($req, $res) {
  //   $admin = AdminBLL::auth($req);
  //   $smsBLL = new SmsBLL();

  //   $input = input('post.');
  //   if(isset($_FILES['image'])) {
  //     $filepath = ROOT_PATH.'public/images/'.$_FILES["image"]["name"];
  //     move_uploaded_file($_FILES["image"]["tmp_name"], $filepath);
  //     $input['image'] = _::file2base64($filepath);
  //     unlink($filepath);
  //   }
  //   $result = $smsBLL->addSign($input);
  //   $res->return($result);
  // },
  // 'delete /v1/admin/sms-sign/:smsId' => function($req, $res) {
  //   $admin = AdminBLL::auth($req);
  //   $smsBLL = new SmsBLL();

  //   $smsBLL->delSign($req->param('smsId'));
  //   $res->success();
  // },
  // 'put /v1/admin/sms-sign/:smsId' => function($req, $res) {
  //   $admin = AdminBLL::auth($req);
  //   $smsBLL = new SmsBLL();

  //   $result = $smsBLL->putSign($req->param('smsId'), input('put.'));
  //   $res->return($result);
  // },
  /**
   * @api {get} /v1/admin/sms-sign 获取签名列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   */
  // 'get /v1/admin/sms-sign' => function($req, $res) {
  //   $admin = AdminBLL::auth($req);
  //   $smsBLL = new SmsBLL();

  //   $result = $smsBLL->getSign();
  //   $res->return($result);
  // },
  /**
   * @api {post} /v1/admin/sms-tpl 添加模板列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} text 内容
   * @apiParam {string} [description] 备注
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
   */
  'delete /v1/admin/sms-tpl/:smsId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $smsBLL->delTpl($req->param('smsId'));
    $res->success();
  },
  /**
   * @api {delete} /v1/admin/sms-tpl-test 批量删除模板
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * @apiParam {array} ids
   */
  'delete /v1/admin/sms-tpl-test' => function($req, $res) {
    $arr = input('delete.');
    $result = wxHelper::delSmsTpl($arr['ids']);
    $res->return($result);
  },
  /**
   * @api {put} /v1/admin/sms-tpl 修改模板列表描述
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} description 备注
   */
  'put /v1/admin/sms-tpl/:smsId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $result = $smsBLL->putTpl($req->param('smsId'), input('put.'));
    $res->return($result);
  },
  /**
   * @api {get} /v1/admin/sms-tpl 获取模板列表
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量,做多50
   */
  'get /v1/admin/sms-tpl' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsBLL = new SmsBLL();

    $hql = $req->paging(function($h) {
      $h['where']['type'] = ['notin',['sign']];
      return $h;
    });
    $results = $smsBLL->getTpl($hql);
    $res->return($results);
  }
]
?>