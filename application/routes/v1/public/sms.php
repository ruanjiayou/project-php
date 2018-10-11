<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/test-sms 测试发送短信
   * @apiGroup public-test-sms
   * 
   * @apiParam {string} phone 手机号
   * @apiParam {string} place 类型
   */
  'get /v1/public/test-sms' => function($req, $res) {
    $data = input('get.');
    $params = [];
    // 1个参数
    if(in_array($data['place'], ['invite','refused','accepted2C','confirmed2C'])) {
      $params = ['参数1'];
    }
    // 2个参数
    if(in_array($data['place'],['forgot','modify','zhuche','accepted2A','canceled2A','canceled2C','canceling2A','canceling2C','withdraw','confirmed','confirmed2A',''])) {
      $params = ['参数1','参数2'];
    }
    // 3个参数
    if(in_array($data['place'],['complaintA2AB','complaintA2CB','complaintC2AB','complaintC2CB','canceling2AB','canceling2CB','canceled2AB','canceled2CB','accepted2AB','accepted2CB'])) {
      $params = ['参数1','参数2','参数3'];
    }
    if(count($params)===0) {
      return $res->return(['detail'=>'发送的短信类型不存在']);
    }
    $messageBLL = new SmsMessageBLL();
    $result = $messageBLL->sendMessage([
      'phone' => $data['phone'],
      'type' => $data['place'],
      'params' => $params
    ]);
    $res->return(['detail'=>'发送成功'], ['detail'=>$result]);
  }
];
?>