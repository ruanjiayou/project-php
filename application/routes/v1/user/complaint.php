<?php
return [
  /**
   * @api {post} /v1/user/invitation/:invitationId/complaint 投诉
   * @apiGroup user-refund
   * 
   * @apiHeader {string} token 鉴权
   * @apiParam {string} complaint 投诉内容
   */
  'post /v1/user/invitation/:invitationId/complaint' => function($req, $res) {
    $user = UserBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $result = $invitationBLL->complaint($req->param('invitationId'), $user['type'] === 'servant' ? 'seller' : 'buyer', input('post.complaint'));

    $res->return($result);
  }
];
?>