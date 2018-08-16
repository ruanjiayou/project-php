<?php
use think\Log;
return [
  'post /v1/public/alipay-cb' => function($req, $res) {
    $data = input('post.');
    $orderBLL = new OrderBLL();
    $order = $orderBLL->getInfo(['order_no'=>$data['out_trade_no']]);
    //$flag = alipayHelper::appPayCb($data);
    if(null === $order) {
      return 'fail';
    } else {
      $orderBLL->update(['trade_no'=>$data['trade_no'],'status'=>'success'], ['id'=>$order['id']]);
      return 'success';
    }
    //return $flag ? 'success' : 'fail';
  }
];
?>