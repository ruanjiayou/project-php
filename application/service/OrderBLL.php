<?php
use \Firebase\JWT\JWT;
use think\Request;

class OrderBLL extends BLL {

  public $table = 'order';
  
  public function create($input) {
    $validation = new Validater([
      'userId' => 'required|int',
      'price' => 'required|int',
      'type' => 'required|enum:recharge,withdraw',
      'status' => 'enum:pending,success,fail|default:"pending"',
      'trade_no' => 'string|default:""',
      'order_no' => 'string|default:""',
      'reason' => 'string|default:""',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $data = $validation->validate($input);
    $isFound = false;
    $order_no = '';
    do {
      $order_no = 'rc-'.time()._::random(10, 'imix');
      $order = self::getInfo(['order_no'=>$order_no]);
      $isFound = $order === null ? false : true;
    } while($isFound);
    $data['order_no'] = $order_no;
    $payInfo = alipayHelper::appPay([
      'body' => '测试充值',
      'subject' => '测试APP支付',
      'out_trade_no' => $data['order_no'],
      'total_amount' => $data['price']
    ]);
    $order = model($this->table)->add($data);
    $order['prepay'] = $payInfo;
    return $order;
  }
}

?>