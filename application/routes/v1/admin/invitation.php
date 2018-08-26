<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/invitations 邀请订单列表
   * @apiGroup admin-invitation
   */
  'get /v1/admin/invitations' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $hql = $req->paging();
    $result = $invitationBLL->getList($hql);
    $res->paging($result);
  }
];

?>