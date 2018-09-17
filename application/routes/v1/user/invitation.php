<?php
return [
  /**
   * @api {post} /v1/user/invitation 邀请
   * @apiGroup user-invitation
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} userId 被邀请人用户id
   * @apiParam {int} priceId 价格id
   * @apiParam {float} x 经度
   * @apiParam {float} y 纬度
   * @apiParam {string} address 邀请地址
   * @apiParam {date} startAt 开始时间
   */
  'post /v1/user/invitation' => function($req, $res) {
    $userBLL = new UserBLL();
    $invitationBLL = new InvitationBLL();
    $user = $userBLL->auth($req);
    
    $result = $invitationBLL->invite($user, input('post.'));
    $res->return($result);
  },
  /**
   * @api {post} /v1/user/invitation/:invitationId 修改邀请订单状态
   * @apiGroup user-invitation
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string='canceling','refused','canceled','accepted','confirmed'} type 状态类型,只有buyer能传canceling,其他是servant的
   */
  'post /v1/user/invitation/:invitationId' => function($req, $res) {
    $userBLL = new UserBLL();
    $invitationBLL = new InvitationBLL();
    $user = $userBLL->auth($req);
    
    $invitationBLL->changeProgress($req->param('invitationId'), input('post.')['type'], $user);
    $res->success();
  },
  /**
   * @api {post} /v1/user/invitation/:invitationId/refund 申请退款(接口不存在)
   * @apiGroup user-invitation
   */
  // 'post /v1/user/invitation/:invitationId/refund' => function($req, $res) {
  //   $invitationBLL = new InvitationBLL();
  //   $user = UserBLL::auth($req);
  //   $invitationBLL->applyRefund($req->param('invitationId'));
  //   $res->success();
  // },
  /**
   * @api {get} /v1/user/invitation 邀请列表
   * @apiGroup user-invitation
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量
   * @apiParam {string='pending','success','fail'} [status] 状态
   */
  'get /v1/user/invitation' => function($req, $res) {
    $user = UserBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $opt = $req->paging(function($hql) use($user) {
      $status = input('get.status');
      if($user['type'] === 'servant') {
        $hql['where']['sellerId'] = $user['id'];
      } elseif($user['type'] === 'buyer') {
        $hql['where']['buyerId'] = $user['id'];
      }
      if(in_array($status, ['pending', 'success', 'fail'])) {
        $hql['where']['status'] = $status;
      }
      $hql['where']['progress'] = ['not in', ['refused','expired']];
      return $hql;
    });
    $result = $invitationBLL->getList($opt);
    $res->paging($result);
  },
  /**
   * @api {get} /v1/user/invitation/:invitationId 邀请详情
   * @apiGroup user-invitation
   * 
   * @apiHeader {string} token 鉴权
   */
  'get /v1/user/invitation/:invitationId' => function($req, $res) {
    $user = UserBLL::auth($req);
    $invitationBLL = new InvitationBLL();
    $query = ['id'=> $req->param('invitationId')];
    if($user['type']==='buyer') {
      $query['buyerId'] = $user['id'];
    } else {
      $query['sellerId'] = $user['id'];
    }
    $result = $invitationBLL->getInfo($query);
    $res->return($result);
  }
];
?>