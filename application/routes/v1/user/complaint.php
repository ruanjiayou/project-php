<?php
return [
  /**
   * @api {post} /v1/user/invitation/:invitationId/complaint 投诉
   * @apiGroup admin-refund
   * 
   * @apiHeader {string} token 鉴权
   */
  'post /v1/user/invitation/:invitationId/complaint' => function($req, $res) {
    $user = UserBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $result = $invitationBLL->complaint($req->param('invitationId'), input('post.money'));
    $res->return($result);
  }
];
?>