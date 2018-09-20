<?php
use think\Log;
return [
  'post /v1/public/alipay-cb' => function($req, $res) {
    $data = input('post.');
    $orderBLL = new OrderBLL();
    $userBillBLL = new UserBillBLL();
    $order = $orderBLL->getInfo(['order_no'=>$data['out_trade_no']]);
    //$flag = alipayHelper::appPayCb($data);
    if(null === $order || $order['status']!=='pending') {
      return 'fail';
    } else {
      $user = (new UserBLL())->getInfo($order['userId']);
      $orderBLL->update(['trade_no'=>$data['trade_no'],'status'=>'success'], ['id'=>$order['id']]);
      $userBillBLL->balance(['type'=>'income','value'=>intval($order['price']),'detail'=>'recharge'], $user);
      return 'success';
    }
    //return $flag ? 'success' : 'fail';
  }
];
?>