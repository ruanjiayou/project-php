<?php
class alipayHelper {
  static public $appId = C_ALI_APPID;
  // 开发者私匙
  static public $appPrivateKey = C_ALI_PRIVATEKEY;
  // 支付宝公匙
  static public $appPublicKey = C_ALI_PUBLICKEY;
  // 回调地址
  static public $cbUrl = C_ALI_PAYCB;

  static function preOrder() {
    $c = new AopClient ();
    $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
    $c->appId = self::$appId;
    $c->rsaPrivateKey = self::$appPrivateKey;
    $c->format = "json";
    $c->charset= "GBK";
    $c->signType= "RSA2";
    $c->alipayrsaPublicKey = self::$appPublicKey;
    //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.open.public.template.message.industry.modify
    $request = new AlipayOpenPublicTemplateMessageIndustryModifyRequest();
    //SDK已经封装掉了公共参数，这里只需要传入业务参数
    //此次只是参数展示，未进行字符串转义，实际情况下请转义
    $request->bizContent = json_encode();
    $response= $c->execute($request);
  }

  /**
   * 调起APP支付前的请求
   * @param {string} body 主体信息
   * @param {string} subject 主题
   * @param {string} out_trade_no 内部订单号
   * @param {int} total_amount 金额,1代表1分
   * @param {string} [timeout_express] 过期时间
   */
  static function appPay($data) {
    $data['product_code'] = 'QUICK_MSECURITY_PAY';
    $data['total_amount'] = (float)$data['total_amount']/100;
    if(!isset($data['timeout_express'])) {
      $data['timeout_express'] = '30m';
    }
    $bizcontent = json_encode($data);
    $aop = new AopClient();
    $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
    $aop->appId = self::$appId;
    $aop->rsaPrivateKey = self::$appPrivateKey;
    $aop->format = "json";
    $aop->charset = "UTF-8";
    $aop->signType = "RSA2";
    $aop->alipayrsaPublicKey = self::$appPublicKey;
    //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
    $request = new AlipayTradeAppPayRequest();
    //SDK已经封装掉了公共参数，这里只需要传入业务参数
    //此次只是参数展示，未进行字符串转义，实际情况下请转义
    $request->setNotifyUrl(self::$cbUrl);
    $request->setBizContent($bizcontent);
    //这里和普通的接口调用不同，使用的是sdkExecute
    $response = $aop->sdkExecute($request);
    //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
    return $response;//就是orderString 可以直接给客户端请求，无需再做处理。
    //$str = 'alipay_sdk=alipay-sdk-php-20180705&app_id=2018081461057335&biz_content=%7B%22body%22%3A%22%5Cu6d4b%5Cu8bd5%5Cu5145%5Cu503c%22%2C%22subject%22%3A%22%5Cu6d4b%5Cu8bd5APP%5Cu652f%5Cu4ed8%22%2C%22out_trade_no%22%3A%22rc-1534387613P8A7GIYP4Z%22%2C%22total_amount%22%3A0.01%2C%22product_code%22%3A%22QUICK_MSECURITY_PAY%22%2C%22timeout_express%22%3A%2230m%22%7D&charset=UTF-8&format=json&method=alipay.trade.app.pay&notify_url=%E5%95%86%E6%88%B7%E5%A4%96%E7%BD%91%E5%8F%AF%E4%BB%A5%E8%AE%BF%E9%97%AE%E7%9A%84%E5%BC%82%E6%AD%A5%E5%9C%B0%E5%9D%80&sign_type=RSA2&timestamp=2018-08-16+10%3A46%3A53&version=1.0&sign=R9GU0oXiZEWMdg%2FYioKr8vFXgPEkjZQtwOVh%2Fggt3z0c%2BVc98BUB13anp159Hw46IvnBlz%2FnT7uyLcF%2BzNrMtCDYBV%2BXPoAxfBtwEjAlQkhoVso55R7OB5nHdpKU3VoQe8hHeRx7Y2gvDb42fq%2BTtUixOdlVIW0ucEXfK4K%2FNarw6R9hB3vvoY77JUxtOymEnyuGwqQ8RSz6AhqGLwYvPa58N9mF4Viw6kF0oyUpq%2Fse0sjjUqTTl4Y%2BsofT%2F%2BbzpYdlnS%2BEesyQe%2Brx%2BkQ%2FgPL0wtb0lmlbY6NFZQM00j%2BeBegZbrEU6OYsFNq6tflb48m8LoYry4h%2BFYORK9YZMg%3D%3D';
    // $arr = explode('&', $response);
    // $o = [];
    // for($i=0;$i<count($arr);$i++) {
    //   list($k, $v) = explode('=', $arr[$i]);
    //   if($k!=='') {
    //     $o[$k] = urldecode($v);
    //   }
    //   if($k === 'biz_content') {
    //     $o[$k] = json_decode($o[$k]);
    //   }
    // }
    // return $o;
  }
  /**
   * 回调
   */
  static function appPayCb($data) {
    $aop = new AopClient();
    $aop->alipayrsaPublicKey = self::$appPublicKey;
    $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
    return $flag;
  }
}
?>