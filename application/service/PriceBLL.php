<?php
use \Firebase\JWT\JWT;
use think\Request;

class PriceBLL extends BLL {

  public $table = 'price';
  static public $table2 = 'price';
  
  static function screate($data) {
    $validation = new Validater([
      'value' => 'required|int',
      'type' => 'required|enum:order,signin|default:"order"'
    ]);
    $input = $validation->validate($data);
    return model(self::$table2)->add($input);
  }

  static function getSignin() {
    return model(self::$table2)->getInfo(['type'=>'signin']);
  }

  static function putSignin($data) {
    $validation = new Validater([
      'value' => 'required|int'
    ]);
    $input = $validation->validate($data);
    return model(self::$table2)->edit(['type'=>'signin'], $input);
  }

  static function getOrders() {
    return model(self::$table2)->getList(['limit'=>0,'order'=>'value DESC','where'=>['type'=>'order']]);
  }

  static function putOrder($data, $orderId) {
    $validation = new Validater([
      'value' => 'required|int'
    ]);
    $input = $validation->validate($data);
    return model(self::$table2)->edit(['id'=>$orderId], $input);
  }
}

?>