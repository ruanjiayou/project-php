<?php
return [
  'updateFail' => [
    'code' => 10810,
    'status' => 200,
    'message' => '修改邀请状态失败,请刷新邀请列表!'
  ],
  'userDontWork' => [
    'code' => 10820,
    'status' => 200,
    'message' => '该用户当天不是工作日!'
  ],
  'dateInvalid' => [
    'code' => 10830,
    'status' => 200,
    'message' => '请选择大于当前的日期进行预约!'
  ],
  'userWorking' => [
    'code' => 10840,
    'status' => 200,
    'message' => '用户正在工作中!'
  ],
  'complainted' => [
    'code' => 10850,
    'status' => 200,
    'message' => '订单已被投诉!'
  ],
  'canceledTooMany' => [
    'code' => 10860,
    'status' => 200,
    'message' => '该用户,24小时内取消邀请超过2次,无法接受邀请!'
  ],
  'willless' => [
    'code' => 10870,
    'status' => 200,
    'message' => '用户现在有事,暂时无法接受邀请!'
  ],
  'repeated' => [
    'code' => 10880,
    'status' => 200,
    'message' => '您已经发过邀请了!'
  ],
  'unfinish' => [
    'code' => 10890,
    'status' => 200,
    'message' => '您有未完成的邀请!'
  ]
];
?>