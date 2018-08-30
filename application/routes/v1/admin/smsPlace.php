<?php
return [
  /**
   * @api {put} /v1/admin/sms-place/:place 更改占位信息:将模板设为使用状态
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} logicId 腾讯云里的ID
   */
  'put /v1/admin/sms-place/:place' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsPlaceBLL = new SmsPlaceBLL();

    $smsPlaceBLL->setTpl($req->param('place'),input('put.logicId'));
    $res->success();
  },
  /**
   * @api {get} /v1/admin/sms-place 获取占位信息列表
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
   *     signId: 1,
   *     sign: '签名',
   *     tplId: 1,
   *     tpl: '模板',
   *     place: 'zhuche',
   *     description: ''
   *   }]
   * }
   */
  'get /v1/admin/sms-place' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsPlaceBLL = new SmsPlaceBLL();
    $result = $smsPlaceBLL->getAll();
    $res->paging($result);
  },
];
?>