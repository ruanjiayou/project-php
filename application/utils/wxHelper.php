<?php
use Qcloud\Sms\SmsSingleSender;

class wxHelper {
  static public $smsAppId = '1400120461';
  static public $smsAppKey = 'ce80ec5dc84a0e03bc4ee0b74f53f4fa';
  static public $cosAppId = '1257241165';
  static public $cosSecretId = 'AKIDALQMDvpq4rYKxPEBJG6EFE2d2GIA6XIr';
  static public $cosSecretKey = 'Yu695sZHpDQRGyVr03wefWDiYJtNfmlf';
  static public $cosBucket = 'banyou';
  static public $cosRegion = 'sh';
  /**
   * 传入手机号生成的是模板的sign,不然就是签名的sign
   */
  static function getSignature($phone='') {
    $random = _::random(10);
    $time = time();
    if($phone!=='') {
      $phone = '&mobile='.$phone;
    }
    $str = sprintf('appkey=%s&random=%s&time=%s%s', self::$smsAppKey, $random, $time, $phone);
    return [
      'random' => $random,
      'time' => $time,
      'sign' => hash('sha256', $str)
    ];
  }
  
  /**
   * 向手机发送短信
   * @param $phone 手机号
   * @param $sign 短信签名
   * @param $tplId 模板id
   * @param $params 参数数组
   * @param $county 国家码
   */
  static function sendSmsMessage($phone, $sign, $tplId, $params, $county = '86') {
    $signature = self::getSignature($phone);
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/sendsms')
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$signature['random']])
      ->send([
        'ext' => '',
        'extend' => '',
        'params' => $params,
        'sig' => $signature['sign'],
        'tel' => [
          'mobile' => $phone,
          'nationcode' => $county
        ],
        'time' => $signature['time'],
        'tpl_id' => $tplId
      ])
      ->end();
    return $result;
  }
  /**
   * 添加短信签名
   */
  static function addSmsSign($input) {
    $validation = new Validater([
      'image' => 'text|alias:pic',
      'remark' => 'string',
      'text' => 'required|string'
    ]);
    $data = $validation->validate($input);
    $signature = self::getSignature();
    $data['sig'] = $signature['sign'];
    $data['time'] = $signature['time'];
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/add_sign')
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$signature['random']])
      ->send($data)
      ->end();
    return $result;
  }
  /**
   * 删除短信签名
   */
  static function delSmsSign($arr) {
    $signature = self::getSignature();
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/del_sign')
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$signature['random']])
      ->send(['sign_id'=>$arr,'sig'=>$signature['sign'],'time'=>$signature['time']])
      ->end();
    return $result;
  }
  /**
   * 修改短信签名: 禁止使用修改
   */
  static function modSmsSign($input) {
    $validation = new Validater([
      'pic' => 'text',
      'remark' => 'string',
      'sign_id' => 'int',
      'text' => 'required|string'
    ]);
    $data = $validation->validate($input);
    $signature = self::getSignature();
    $data['sig'] = $signature['sign'];
    $data['time'] = $signature['time'];
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/mod_sign')
      ->query(['sdkappid'=>self::$smsAppId,'time'=>$signature['time'],'random'=>$signature['random']])
      ->send($data)
      ->end();
    return $result;
  }
  /**
   * 获取短信签名状态
   */
  static function getSmsSign($arr) {
    $signature = self::getSignature();
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/get_sign')
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$signature['random']])
      ->send(['sig'=>$signature['sign'],'sign_id'=>$arr,'time'=>$signature['time']])
      ->end();
    return $result['result'] === 0 ? $result['data'] : [];
  }

  /**
   * 添加模板
   */
  static function addSmsTpl($input) {
    $validation = new Validater([
      'text' => 'required|string',
      'remark' => 'string',
      'type' => 'required|string|default:0'
    ]);
    $data = $validation->validate($input);
    $sign = self::getSignature();
    $data['time'] = $sign['time'];
    $data['sig'] = $sign['sign'];
    // $url = 'http://'.$_SERVER['HTTP_HOST'].'/test/shttp/post';
    $url = 'https://yun.tim.qq.com/v5/tlssmssvr/add_template';
    $result = shttp::post($url)
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$sign['random']])
      ->send($data)
      ->end();
    return $result;
  }
  static function delSmsTpl($arr) {
    $signature = self::getSignature();
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/del_template')
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$signature['random']])
      ->send(['sig'=>$signature['sign'],'time'=>$signature['time'],'tpl_id'=>$arr])
      ->end();
    return $result;
  }
  static function getSmsTpl($o=[]) {
    $sign = self::getSignature();
    $query = [
      'sig' =>$sign['sign'],
      'time' =>$sign['time']
    ];
    if(_::isArray($o) && count($o)>0) {
      $query['tpl_id'] = $o;
    } else {
      $page = 1;
      $limit = 50;
      if(_::isObject($o)) {
        if(_::isInt($o['limit']) && $o['limit']>0 && $o['limit']<=50) {
          $limit = $o['limit'];
        }
        if(_::isInt($o['page']) && $o['page']>0) {
          $page = $o['page'];
        }
      }
      $query['tpl_page'] = [
        'offset' => ($page-1)*$limit,
        'max' => $limit
      ];
    }
    $result = shttp::post('https://yun.tim.qq.com/v5/tlssmssvr/get_template')
      ->query(['sdkappid'=>self::$smsAppId,'random'=>$sign['random']])
      ->send($query)
      ->end();
    if($result['result']!==0) {
      thrower('common', 'thirdApiFail', $result['msg']);
    }
    return $result['data'];
  }

  static function addFile($key, $file) {
    $cosClient = new Qcloud\Cos\Client([
      'region' => self::$cosRegion,
      'credentials' => [
        'secretId' => self::$cosSecretId,
        'secretKey' => self::$cosSecretKey
      ]
    ]);
    $result = $cosClient->putObject([
      'Bucket' => self::$cosBucket.'-'.self::$cosAppId,
      'Key' => $key,
      'Body' => $file
    ]);
    return $result;
  }
}

?>