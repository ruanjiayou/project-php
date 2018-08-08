<?php
use \Firebase\JWT\JWT;
use think\Request;

class SigninBLL extends BLL {
  
  /**
   * 签到业务
   * 1.判断今天是否签到,是跳到5
   * 2.查找奖励
   * 3.签到
   * 4.添加奖励
   * 5.返回
   */
  static function signin($user) {
    $query = ['userId'=>$user['id'], 'createdAt'=>date('Y-m-d')];
    $sign = model('signin')->getInfo($query);
    if(null !== $sign) {
      thrower('sign', 'signed');
    }
    $price = model('price')->getInfo(['type'=>'signin']);
    if(null === $price) {
      thrower('sign', 'signPriceFail');
    }
    $sign = model('signin')->add($query);
    $query['type'] = 'income';
    $query['value'] = $price['value'];
    model('user_bill')->add($query);
    model('user')->edit(['id'=>$user['id']], ['money'=>$price['value']+$user['money']]);
    return $sign;
  }

  static function monthSigns($userId, $input) {
    $validation = new Validater([
      'year' => 'int',
      'month'=> 'int'
    ]);
    $query = $validation->validate($input);
    if(!isset($query['year'])) {
      $query['year'] = date('Y');
    }
    if(!isset($query['month'])) {
      $query['month'] = date('m');
    }
    $where = ['userId'=>$userId, 'createdAt'=>['like', $query['year'].'-'.$query['month'].'-'.'%']];
    $result = model('signin')->getList(['where'=>$where, 'limit'=>0]);
    return $result;
  }

  static function signed($userId) {
    $query = ['userId'=>$userId, 'createdAt'=>date('Y-m-d')];
    $sign = model('signin')->getInfo($query);
    return null !== $sign;
  }

  static function signs($userId) {
    //TODO: 获取本月签到记录
  }
}