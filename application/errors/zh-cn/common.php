<?php

return [
  'unknown' => [
    'code' => 10110,
    'status' => 200,
    'message' => '内部服务器未知错误'
  ],
  'validation' => [
    'code' => 10120,
    'status' => 200,
    'message' => '字段验证未通过!'
  ],
  'notFound' => [
    'code' => 10130,
    'status' => 200,
    'message' => '资源不存在或已被删除!'
  ],
  'thirdApiFail' => [
    'code' => 10140,
    'status' => 200,
    'message' => '请求第三方接口错误!'
  ]
];

?>