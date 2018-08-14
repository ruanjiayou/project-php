<?php
return [
  /**
   * @api {post} /v1/admin/invitation/:invitationId/refund 退款
   * @apiGroup admin-refund
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} money 退款
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
  'post /v1/admin/invitation/:invitationId/refund' => function($req, $res) {
    $user = UserBLL()->auth($req);
    $invitationBLL = new InvitationBLL();

    $result = $invitationBLL->refund($req->param('invitationId'), input('post.money'));
    $res->return($result);
  }
];
?>