<?php
use app\model;
use think\Request;
use think\Response;

return [
  'get /v1/admin/invitations' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $hql = $req->paging();
    $result = $invitationBLL->getList($hql);
    $res->paging($result);
  }
];

?>