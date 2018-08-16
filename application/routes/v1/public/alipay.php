<?php
use think\Log;
return [
  'post /v1/public/alipay-cb' => function($req, $res) {
    /*
    {
      "gmt_create":"2018-08-16 15:56:13",
      "charset":"UTF-8",
      "seller_email":"804584271@qq.com",
      "subject":"\u6d4b\u8bd5APP\u652f\u4ed8",
      "sign":"BZ\/Gky07EevPYW4kRVTNREmU0\/qNw\/nj\/XS\/k+MTs0BmZvksfume5ziXA81U\/t2AVMW6UcF9VnXB3ReGjuQDwEVbIwil4rhh9RhgFQx+ajWfCmmGk5uApmjCRxcKyainAk+FqCFLcqoCHpTK2vFBjJ4WEfDmH\/o27TKYbPfmr\/Yr1HKj9oJ6AvM0\/3bDOj5elqzECc3e8UI0HiYPdo+5APE0BUDV6Ybq8+XQAb6gA15+ImyicSDe+uVWpHQA6\/D7+XG2P6IZz7SRJGq3zgUFbDwnYsZnKtMkMf8JnK2Socd2DKePnlTAgo\/06TQUILAbVQ\/iscCWh\/C2sAEdVOPa9g==",
      "body":"\u6d4b\u8bd5\u5145\u503c",
      "buyer_id":"2088902500887856",
      "invoice_amount":"0.01",
      "notify_id":"e668e2ad2f96db09d795bda1cea5    bcfmk9",
      "fund_bill_list":"[{\"amount\":\"0.01\",\"fundChannel\":\"ALIPAYACCOUNT\"}]",
      "notify_type":"trade_status_sync",
      "trade_status":"TRADE_SUCCESS",
      "receipt_amount":"0.01",
      "app_id":"2018081461057335",
      "buyer_pay_amount":"0.01",
      "sign_type":"RSA2",
      "seller_id":"2088621983132225",
      "gmt_payment":"2018-08-16 15:56:14",
      "notify_time":"2018-08-16 15:56:14","version":"1.0",
      "out_trade_no":"rc-153440616739JCNK5MAC",
      "total_amount":"0.01",
      "trade_no":"2018081621001004850578260399",
      "auth_app_id":"2018081461057335",
      "buyer_logon_id":"189****6482",
      "point_amount":"0.00"
    }
    */
    $data = input('post.');
    // TODO: 根据out-trade_no查询order,验证订单.并修改订单状态
    $order = null;

    return null===$order ? 'fail' : 'success';
  }
];
?>