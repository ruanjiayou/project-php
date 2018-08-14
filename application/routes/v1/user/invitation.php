<?php
return [
  /**
   * @api {post} /v1/user/invitation 邀请
   * @apiGroup user-invitation
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} userId 被邀请人用户id
   * @apiParam {int} price 价格
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
   * @api {put} /v1/user/invitation/:invitationId 修改邀请订单状态
   * @apiGroup user-invitation
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string='canceling','refused','canceled','accepted','comfirmed'} type 状态类型,只有buyer能传canceling,其他是servant的
   */
  'put /v1/user/invitation/:invitationId' => function($req, $res) {
    $userBLL = new UserBLL();
    $invitationBLL = new InvitationBLL();
    $user = $userBLL->auth($req);

    $invitationBLL->changeProgress($req->param('invitationId'), input('put.type'), $user);
    $res->success();
  },
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

    $opt = $req->paging(function($hql) {
      $status = input('get.status');
      if(in_array($status, ['pending', 'success', 'fail'])) {
        $hql['where']['status'] = $status;
      }
      return $hql;
    });
    $result = $invitationBLL->getList($opt);
    $res->paging($result);
  }
];
?>