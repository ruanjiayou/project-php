<?php
return [
  'userNotFound' => [
    'code' => 10210,
    'status' => 200,
    'message' => '该账号不存在!'
  ],
  'phoneRegistered' => [
    'code' => 10220,
    'status' => 200,
    'message' => '手机号已被注册!'
  ],
  'passwordError' => [
    'code' => 10230,
    'status' => 200,
    'message' => '账号或密码错误!'
  ],
  'approving' => [
    'code' => 10240,
    'status' => 403,
    'message' => '申请中!'
  ],
  'unapproved' => [
    'code' => 10250,
    'status' => 401,
    'message' => '申请失败!'
  ],
  'moneyLess' => [
    'code' => 10260,
    'status' =>  200,
    'message' => '余额不足!'
  ],
  'forbidden' => [
    'code' => 10270,
    'status' =>  200,
    'message' => '账号已被锁定!'
  ],
  'phoneNotFound' => [
    'code' => 10280,
    'status' =>  200,
    'message' => '该手机号没有注册!'
  ]
];
?>