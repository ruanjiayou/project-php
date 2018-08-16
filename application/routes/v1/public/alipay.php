<?php
use think\Log;
return [
  'post /v1/public/alipay-cb' => function($req, $res) {
    $data = input('post.');
    Log::write(json_encode($data));
    $flag = alipayHelper::appPayCb($data);
    return $flag ? 'success' : 'fail';
  }
];
?>