<?php
return [
  /**
   * @api {put} /v1/admin/sms-place/:place 更改签名或模板
   * @apiGroup admin-sms
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {type='sign','tpl'} 类型
   * @apiParam {int} logicId 腾讯云里的ID
   */
  'put /v1/admin/sms-place/:place' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $smsPlaceBLL = new SmsPlaceBLL();

    $place = $req->param('place');
    $data = input('put.');
    if($data['type'] === 'sign') {
      $smsPlaceBLL->setSign($place, $data['logicId']);
    } else {
      $smsPlaceBLL->setTpl($place, $data['logicId']);
    }
    $res->success();
  },
  /**
   * @api {get} /v1/admin/sms-place 获取短信列表
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