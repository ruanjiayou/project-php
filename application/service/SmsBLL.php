<?php
use \Firebase\JWT\JWT;
use think\Request;

class SmsBLL extends BLL {
  
  public $table = 'sms';

  /**
   * 添加短信签名
   */
  function addSign($input) {
    $validation = new Validater([
      'title' => 'required|string',
      'image' => 'required|text',
      'description' => 'required|empty|string|default:""',
      'type' => 'required|string|default:"sign"',
      'status' => 'required|string|default:"pending"',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    $sign = wxHelper::addSmsSign($data);
    $data['logicId'] = $sign['data']['id'];
    $result = null;
    if($sign['result'] === 0) {
      $result = model($this->table)->add($data);
    } else {
      thrower('sms', 'addSignFail', $sign['result'].' '.$sign['msg']);
    }
    return $result;
  }
  /**
   * 删除短信签名
   */
  function delSign($smsId) {
    $signModel = model($this->table);
    $query = [$signModel->primaryKey=>$smsId, 'type'=>'sign'];
    $sign = $signModel->getInfo($query);
    if($sign !== null && $sign['status'] === 'using') {
      thrower('sms', 'smsSignUsing');
    } else {
      // 删除数据库的同事 同步 微信平台
      wxHelper::delSmsSign([$sign['logicId']]);
      return $signModel->destroy($query);
    }
  }
  /**
   * 
   */
  function putSign($smsId, $input) {
    $validation = new Validater([
      'type' => 'required|string|default:"sign"',
      'place' => 'required|string'
    ]);
    $data = $validation->validate($input);
    $signModel = model($this->table);
    $query = [$signModel->primaryKey=>$smsId, 'type'=>$data['type']];
    $sign = $signModel->getInfo($query);
    if($sign === null) {
      thrower('common', 'notFound');
    }
    $older = $signModel->edit($data, ['place'=>'none', 'status'=>'success']);
    $sign = $signModel->edit($query, ['place'=>$data['place'],'status'=>'using']);
    return $sign;
  }
  /**
   * 获取所有签名并刷新状态
   */
  function getSign() {
    $results = model($this->table)->getList(['where'=>['type'=>'sign'], 'limit'=>0])->items();
    $ids = [];
    // TODO: 不只是同步状态 还有其他字段
    for($n=0;$n<count($results);$n++) {
      if($results[$n]['status'] === 'pending') {
        array_push($ids, intval($results[$n]['logicId']));
      }
    }
    $signs = wxHelper::getSmsSign($ids);
    $res = [];
    for($j=0;$j<count($signs);$j++) {
      $sign = $signs[$j];
      $found = false;
      for($i=0;$i<count($results);$i++) {
        $result = $results[$i];
        if($sign['id'] === intval($result['logicId'])) {
          $found = true;
          if($result['status'] === 'pending') {
            if($sign['status'] === 0) {
              $result = model($this->table)->edit(['id'=>$result['id'], 'logicId'=>$result['logicId']], ['status'=>'success']);
            } else if($sign['status'] ===2) {
              $result = model($this->table)->edit(['id'=>$result['id'], 'logicId'=>$result['logicId']], ['status'=>'fail', 'reason'=>$sign['reply']]);
            }
          }
          array_push($res, $result);
        }
      }
      if($found===false) {
        // 1: 审核中 0: 成功 2: 失败
        $status = ['success', 'pending', 'fail'];
        $result = model($this->table)->add([
          'logicId'=>$sign['id'],
          'title'=>$sign['text'],
          'type'=>'sign',
          'status'=>$status[$sign['status']],
          'reason'=>$sign['reply'],
          'createdAt' => date('Y-m-d H:i:s')
        ]);
        array_push($res, $result);
      }
    }
    return $res;
  }
  function addTpl($input) {
    $tpl = wxHelper::addSmsTpl($input);
    $result = null;
    if($tpl['result'] === 0) {
      $result = model($this->table)->add([
        'logicId' => $tpl['data']['id'],
        'title' => $input['title'],
        'content' => $input['text'],
        'type' => 'common',
        'status' => 'pending',
        'createdAt' => date('Y-m-d H:i:s')
      ]);
    } else {
      thrower('sms', 'addTplFail', $tpl['result'].' '.$tpl['msg']);
    }
    return $result;
  }
  function delTpl() {

  }
  function putTpl() {

  }
  function getTpl($hql) {
    $results = model($this->table)->getList($hql);
    $ids = array_map(function($item){
      return intval($item['logicId']);
    }, $results->items());
    $tpls = wxHelper::getSmsTpl($ids);
    // TODO: 更新模板状态和其他
    //dump($tpls);
    //  'id' => int 170055
    //  'international' => int 0
    //  'text' => string '{1}为您的验证码，请于{2}分钟内填写' (length=48)
    //  'status' => int 0
    //  'type' => int 0
    //  'reply' => string '' (length=0)
    //  'title' => string '注册和修改密码' (length=21)
    //  'apply_time' => string '2018-08-07 16:39:08' (length=19)
    //  'reply_time' => string '2018-08-07 16:40:34' (length=19)
    return $results;
  }
  function sendMessage($input) {
    $validation = new Validater([
      'phone' => 'required|string',
      'type' => 'required|enum:forgot,modify,zhuche,system,invite,cancel,refused,accepted,canceled',
      'params' => 'required|array'
    ]);
    $data = $validation->validate($input);
    $sign = model($this->table)->getInfo(['type'=>'sign','status'=>'success']);
    $place = model('sms_place')->getInfo(['place'=>$data['type']]);
    if($sign===null) {
      thrower('sms', 'signNotFound');
    }
    if($place === null) {
      thrower('sms', 'tplNotFound');
    }
    $tpl = model($this->table)->getInfo(['id'=>$place['smsId']]);
    $message = model('sms_message')->add([
      'smsId' => $tpl['id'],
      'tpl' => $tpl['content'],
      'json' => json_encode($data['params']),
      'phone' => $data['phone'],
      'type' => $data['type'],
      'status' => 'success',
      'createdAt' => date('Y-m-d H:i:s')
    ]);
    $result = wxHelper::sendSmsMessage($data['phone'], $sign['title'], $tpl['logicId'], $data['params']);
    if($result['result']!==0) {
      model('sms_message')->edit(['id'=>$tpl['id']], ['status'=>'fail']);
      thrower('sms', 'smsSendFail', $result['errmsg']);
    }
    return $result;
  }
}
?>