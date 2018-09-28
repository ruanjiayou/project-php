<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/admin/invitations 邀请订单列表,评论列表
   * @apiGroup admin-invitation
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量
   * @apiParam {int} [buyerId] 用户id
   * @apiParam {int} [sellerId] 用户id
   * @apiParam {string='pending', 'success', 'fail'} [status] 邀请订单状态
   * @apiParam {string='inviting','refused','canceling','canceled','accepted','confirmed','expired'} [progress] 邀请订单进度
   * @apiParam {string='no','refunding','yes', 'all'} isRefund 退款状态
   * @apiParam {int=0,1} isComplaint 投诉邀请订单
   * @apiParam {string} [search] 卖家昵称或手机号
   * @apiParam {string='sellerComment','buyerComment} [type] 评论
   */
  'get /v1/admin/invitations' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $validation = new validater([
      'status' => 'enum:pending,success,fail|ignore',
      'progress' => 'enum:inviting,refused,canceling,canceled,accepter,confirmed,expired|ignore',
      'type' => 'string',
      'isComplaint' => 'int|ignore',
      'status' => 'enum:pending,success,fail|ignore',
      'buyerId' => 'int|ignore',
      'sellerId' => 'int|ignore',
      'isRefund' => 'int|ignore',
      'search' => 'string'
    ]);
    $query = $validation->validate(input('get.'));
    $invitationBLL = new InvitationBLL();
    $hql = $req->paging(function($h) use($query){
      if(isset($query['buyerId'])) {
        $h['where']['buyerId'] = $query['buyerId'];
      }
      if(isset($query['sellerId'])) {
        $h['where']['sellerId'] = $query['sellerId'];
      }
      if(isset($query['status'])) {
        $h['where']['status'] = $query['status'];
      }
      if(isset($query['progress'])) {
        $h['where']['progress'] = $query['progress'];
      }
      if(isset($query['isRefund'])) {
        $h['where']['isRefund'] = $query['isRefund'] == '1'? 1 : 0;
      }
      if(isset($query['isComplaint'])) {
        $h['where']['isComplaint'] = $query['isComplaint'] == '1' ? 1 : 0;
      }
      if(isset($query['type'])) {
        if($query['type']==='sellerComment') {
          $h['where']['isComment'] = ['in', ['yes','sold']];
        }
        if($query['type']==='buyerComment') {
          $h['where']['isComment'] = ['in', ['yes','bought']];
        }
      }
      return $h;
    });
    if(isset($hql['search'])&&$hql['search']!=='') {
      $hql['where']['buyerPhone|sellerPhone'] = ['like', '%'.$hql['search'].'%'];
    }
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
    $userBLL = new UserBLL();

    $result = $invitationBLL->getInfo($req->param('invitationId'));
    if(null !== $result) {
      $result['buyerAgency'] = $userBLL->getInfo(['id'=>$result['buyerAgencyId']], ['field'=>'id,nickName,phone']);
      $result['sellerAgency'] = $userBLL->getInfo(['id'=>$result['sellerAgencyId']], ['field'=>'id,nickName,phone']);
    }
    $res->return($result);
  },
  /**
   * @api {put} /v1/admin/invitations/:invitationId/refund 接受退款/退款成功(api不存在)
   * @apiGroup admin-invitation
   * @apiParam {int} [money] 玫瑰数额,没传就是接受退款
   */
  // 'put /v1/admin/invitations/:invitationId/refund' => function($req, $res) {
  //   $admin = AdminBLL::auth($req);
  //   $data = input('put.');
  //   if(isset($data['money'])) {
  //     $result = (new InvitationBLL())->refund($req->param('invitationId'),input('put.money'));
  //     $res->return($result);
  //   } else {
  //     $result = (new InvitationBLL())->acceptRefund($req->param('invitationId'));
  //     $res->return($result);
  //   }
  // }
  
  /**
   * @api {put} /v1/admin/invitations/:invitationId/refund 处理投诉邀请订单
   * @apiGroup admin-invitation
   */
  'put /v1/admin/invitations/:invitationId/refund' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();
    $userBillBLL = new UserBillBLL();
    $invitationBLL = new InvitationBLL();

    $invitation = $invitationBLL->getInfo($req->param('invitationId'));
    if($invitation['isRefund']==1) {
      return $res->fail();
    }
    $result = $invitationBLL->update(['isRefund'=>1], $req->param('invitationId'));

    $seller = $userBLL->getInfo($invitation['sellerId']);
    $sellerAgency = $userBLL->getInfo($invitation['sellerAgencyId']);
    $buyerAgency = $userBLL->getInfo($invitation['buyerAgencyId']);
    // 中介返利
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['rebateAgency'],
      'detail' => 'seller-cashback'
    ], $sellerAgency);
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['rebateAgency'],
      'detail' => 'buyer-cashback'
    ], $buyerAgency);
    // 卖家进账
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['rebate'],
      'detail' => 'invitation'
    ], $seller);
    // 平台收入
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['price']-$invitation['rebateAgency']*2-$invitation['rebate'],
      'detail' => 'platformIncome'
    ]);
    $res->success();
  },
  /**
   * @api {put} /v1/admin/invitations/:invitationId 处理投诉邀请订单
   * @apiGroup admin-invitation
   */
  'put /v1/admin/invitations/:invitationId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();
    $userBillBLL = new UserBillBLL();
    $invitationBLL = new InvitationBLL();

    $invitation = $invitationBLL->getInfo($req->param('invitationId'));
    $result = $invitationBLL->update(['isRefund'=>1], $req->param('invitationId'));

    $seller = $userBLL->getInfo($invitation['sellerId']);
    $sellerAgency = $userBLL->getInfo($invitation['sellerAgencyId']);
    $buyerAgency = $userBLL->getInfo($invitation['buyerAgencyId']);
    // 中介返利
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['rebateAgency'],
      'detail' => 'seller-cashback'
    ], $sellerAgency);
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['rebateAgency'],
      'detail' => 'buyer-cashback'
    ], $buyerAgency);
    // 卖家进账
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['rebate'],
      'detail' => 'invitation'
    ], $seller);
    // 平台收入
    $userBillBLL->balance([
      'type' => 'income',
      'value' => $invitation['price']-$invitation['rebateAgency']*2-$invitation['rebate'],
      'detail' => 'platformIncome'
    ]);
    $res->success();
  }
];

?>