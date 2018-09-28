<?php
require __DIR__ . '/../../public/index.php';
use think\Log;

function logger($str) {
  $fh = fopen(LOG_PATH.'test.log', 'a+');
  fwrite($fh,$str.'\r\n');
  fclose($fh);
}
/**
 * 自动返现
 * @param $id 邀请订单id
 */
function rebate($id) {
  $invitation = db('invitation')->where(['id'=>$id])->find();
  if(!empty($invitation) || $invitation['isExpired']==1) {
    $seller = db('user')->where(['id'=>$invitation['sellerId']])->find();
    $sellerAgency = db('user')->where(['id'=>$invitation['sellerAgencyId']])->find();
    $buyerAgency = db('user')->where(['id'=>$invitation['buyerAgencyId']])->find();
    // 卖家自动进账
    db('user')->where(['id'=>$seller['id']])->update(['money'=>$seller['money']+$invitation['rebate']]);
    $id1 = db('user_bill')->insertGetId([
      'userId' => $seller['id'],
      'type' => 'income',
      'value' => $invitation['rebate'],
      'detail' => 'invitation',
      'createdAt' => date('Y-m-d H:i:s')
    ]);
    // 中介自动进账
    // A上级
    db('user')->where(['id'=>$sellerAgency['id']])->update(['money'=>$sellerAgency['money']+$invitation['rebateAgency']]);
    $id2 = db('user_bill')->insertGetId([
      'userId' => $seller['id'],
      'type' => 'income',
      'value' => $invitation['rebateAgency'],
      'detail' => 'seller-cashback',
      'createdAt' => date('Y-m-d H:i:s')
    ]);
    // C上级
    db('user')->where(['id'=>$buyerAgency['id']])->update(['money'=>$buyerAgency['money']+$invitation['rebateAgency']]);
    $id3 = db('user_bill')->insertGetId([
      'userId' => $seller['id'],
      'type' => 'income',
      'value' => $invitation['rebateAgency'],
      'detail' => 'buyer-cashback',
      'createdAt' => date('Y-m-d H:i:s')
    ]);
    // 平台进账
    $id4 = db('user_bill')->insertGetId([
      'userId' => 0,
      'type' => 'income',
      'value' => $invitation['price']-$invitation['rebateAgency']*2-$invitation['rebate'],
      'detail' => 'platformIncome',
      'createdAt' => date('Y-m-d H:i:s')
    ]);
    Log::record('卖家当前余额:'.$seller['money']);
    Log::record('卖家上级当前余额:'.$sellerAgency['money']);
    Log::record('买家上级当前余额:'.$buyerAgency['money']);
    Log::record('卖家进账:'.$invitation['rebate']);
    Log::record('卖家上级进账:'.$invitation['rebateAgency']);
    Log::record('买家上级进账:'.$invitation['rebateAgency']);
    Log::record('账单明细id:');
    Log::record([$id1,$id2,$id3,$id4]);
    Log::record($invitation);
  } else {
    //logger('empty');
  }
}

$tasks = db('task')->where(['type' => 'invitation'])->limit(0)->select();
$ids = [];
for($i=0;$i<count($tasks);$i++) {
  $task = $tasks[$i];
  array_push($ids, $tasks[$i]['id']);
  if($task['type'] === 'invitation') {
    rebate($task['taskId']);
  }
}
if(!empty($ids)) {
  db('task')->where(['id'=>['in', $ids]])->delete();
}
