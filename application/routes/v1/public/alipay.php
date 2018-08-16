<?php
use think\Log;
return [
  'post /v1/public/alipay-cb' => function($req, $res) {
    $data = input('post.');
    Log::write($data);
    dump($data);
    $flag = alipayHelper::appPayCb($data);
    if($flag) {
      $res->success();
    } else {
      $res->fail();
    }
  }
];
?>