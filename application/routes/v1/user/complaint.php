<?php
return [
  /**
   * @api {post} /v1/user/invitation/:invitationId/complaint 投诉
   * @apiGroup user-refund
   * 
   * @apiHeader {string} token 鉴权
   * @apiParam {string} complaint
   */
  'post /v1/user/invitation/:invitationId/complaint' => function($req, $res) {
    $user = UserBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $data = [
      'id' => $req->param('invitationId'),
      'type' => $user['type'],
      'complaint' => input('post.complaint')
    ];
    $result = $invitationBLL->complaint($data);
    $res->return($result);
  }
];
?>