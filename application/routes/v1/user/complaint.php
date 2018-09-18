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
    $userBLL = new UserBLL();
    $smsMesageBLL = new SmsMessageBLL();
    $user = $userBLL::auth($req);
    $invitationBLL = new InvitationBLL();

    $result = $invitationBLL->complaint($req->param('invitationId'), $user['type'] === 'servant' ? 'seller' : 'buyer', input('post.complaint'));

    $sellerAgency = $userBLL->getInfo($result['sellerAgencyId']);
    $buyerAgency = $userBLL->getInfo($result['buyerAgencyId']);
    if($user['type'] === 'servant') {
      // A投诉
      // 发送给A的上级
      $smsMesageBLL->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'complaintA2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $result['sellerName'], $result['startAt']]
      ]);
      // 发送给C的上级
      $smsMesageBLL->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'complaintA2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $result['buyerName'], $invitation['startAt']]
      ]);
    } else {
      // C投诉
      // 发送给A的上级
      $smsMesageBLL->sendMessage([
        'phone' => $sellerAgency['phone'],
        'type' => 'complaintC2AB',
        'cid' => $sellerAgency['cid'],
        'params' => [$sellerAgency['nickName'], $result['sellerName'], $result['startAt']]
      ]);
      // 发送给C的上级
      $smsMesageBLL->sendMessage([
        'phone' => $buyerAgency['phone'],
        'type' => 'complaintC2CB',
        'cid' => $buyerAgency['cid'],
        'params' => [$buyerAgency['nickName'], $result['buyerName'], $invitation['startAt']]
      ]);
    }
    $res->return($result);
  }
];
?>