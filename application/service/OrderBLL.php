<?php
use \Firebase\JWT\JWT;
use think\Request;

class OrderBLL extends BLL {

  public $table = 'order';
  
  function create($input) {
    $validation = new Validater([
      'userId' => 'required|int',
      'phone' => 'string',
      'price' => 'required|int',
      'origin' => 'empty|string|default:""',
      'type' => 'required|enum:recharge,withdraw',
      'status' => 'enum:pending,success,fail|default:"pending"',
      'trade_no' => 'string|default:""',
      'order_no' => 'string|default:""',
      'reason' => 'string|default:""',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $data = $validation->validate($input);
    $user = (new UserBLL())->getInfo($data['userId']);
    // 支付宝乘100 微信呢?
    $realMoney = $data['price'] * 100;
    // if($data['price'] === 0 || fmod($data['price'],100)!=0) {
    //   thrower('order', 'not100x');
    // }
    if($data['type']==='recharge') {
      $isFound = false;
      $order_no = '';
      do {
        $order_no = 'rc-'.time()._::random(10, 'imix');
        $order = self::getInfo(['order_no'=>$order_no]);
        $isFound = $order === null ? false : true;
      } while($isFound);
      $data['order_no'] = $order_no;
      $payInfo = alipayHelper::appPay([
        'body' => '充值',
        'subject' => '商务之星平台充值',
        'out_trade_no' => $data['order_no'],
        'total_amount' => $realMoney
      ]);
      $order = model($this->table)->add($data);
      $order['prepay'] = $payInfo;
      return $order;
    } else {
      // 提现就扣,失败再返还
      $order = model($this->table)->add($data);
      $createdAt = substr($data['createdAt'],5,-3);
      // 发送提现消息
      (new SmsMessageBLL())->sendMessage([
        'phone' => $data['phone'],
        'type' => 'withdraw',
        'params' => [$user['nickName'], $createdAt]
      ]);
      (new UserBillBLL())->balance([
        'type' => 'expent',
        'value' => $data['price'],
        'detail' => 'withdraw'
      ], $user);
      return $order;
    }
  }

  function withdraw($condition, $type = 'success') {
    $order = $this->getInfo($condition);
    if($type === 'success') {
      $this->update(['status'=>'success'], $condition);
    } else {
      $user = (new UserBLL())->getInfo($order['userId']);
      $this->update(['status'=>'fail'], $condition);
      (new UserBillBLL())->balance([
        'type' => 'income',
        'value' => $order['price'],
        'detail' => 'withdraw-fail-back'
      ], $user);
    }
    return true;
  }
}

?>