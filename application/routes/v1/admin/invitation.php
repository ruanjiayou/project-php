<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/invitations 邀请订单列表
   * @apiGroup admin-invitation
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量
   * @apiParam {string='pending', 'success', 'fail'} [status] 邀请订单状态
   * @apiParam {string='inviting','refused','canceling','canceled','accepted','confirmed','expired'} [progress] 邀请订单进度
   * @apiParam {string} [search] 卖家昵称或手机号
   * @apiParam {string='refunding','refunded'} type,refunding:处理中,refunded:已退款
   */
  'get /v1/admin/invitations' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $validation = new validater([
      'status' => 'enum:pending,success,fail|ignore',
      'progress' => 'enum:inviting,refused,canceling,canceled,accepter,confirmed,expired|ignore',
      'type' => 'string'
    ]);
    $query = $validation->validate(input('get.'));
    $invitationBLL = new InvitationBLL();
    $hql = $req->paging(function($h) use($query){
      $type = $query['type'];
      unset($query['type']);
      if($type === 'refunding') {
        $h['where']['isComplaint'] = 1;
        $h['where']['isRefund'] = 0;
      }
      if($type === 'refunded') {
        $h['where']['isRefund'] =1;
      }
      return $h;
    });
    $result = $invitationBLL->getList($hql);
    $res->paging($result);
  },
  /**
   * @api {get} /v1/admin/invitations/:invitationId 邀请详情
   * @apiGroup admin-invitation
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * buyerAgency 买家的上级中介
   * sellerAgency 卖家的上级中介
   */
  'get /v1/admin/invitations/:invitationId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $result = $invitationBLL->getInfo($req->param('invitationId'));
    if(null !== $result) {
      $result['buyerAgency'] = (new UserBLL())->getInfo(['id'=>$result['agencyId']], ['field'=>'nickName,phone']);
      $result['sellerAgency'] = (new UserBLL())->getInfo(['id'=>$result['agencyId']], ['field'=>'nickName,phone']);
    }
    $res->return($result);
  }
];

?>