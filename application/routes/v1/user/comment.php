<?php
return [
  /**
   * @api {post} /v1/user/invitation/:invitationId/comment 评论
   * @apiGroup user-invitation-comment
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} score 分数
   * @apiParam {string} comment 评论
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
  'post /v1/user/invitation/:invitationId/comment' => function($req, $res) {
    $user = UserBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $input = input('post.');
    $input['id'] = $req->param('invitationId');
    $input['type'] = $user['type'];
    $result = $invitationBLL->comment($user, $input);
    $res->return($result);
  }
];
?>