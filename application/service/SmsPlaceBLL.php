<?php
use \Firebase\JWT\JWT;
use think\Request;

class SmsPlaceBLL extends BLL {
  
  public $table = 'sms_place';

  function setSign($place, $signId) {
    $smsBLL = new SmsBLL();
    // 1.签名不存在或者不可用,报错
    $sign = $smsBLL->getInfo(['logicId'=>$signId,'type'=>'sign']);
    if($sign === null || $sign['status']==='pending' || $sign['status']==='fail') {
      thrower('sms', 'signNotFound');
    }
    // 2.签名设为使用中,不可被删除
    if($sign['status'] === 'success') {
      $smsBLL->edit(['logicId'=>$signId], ['status'=>'using']);
    }
    // 3.更改占位短信的签名
    $place = $this->getInfo(['place'=>$place]);
    $oldId = $place['signId'];
    $this->update(['signId'=>$sign['logicId'],'sign'=>$sign['text']], ['place'=>$place['place']]);
    // 4.如果旧签名没有被引用,旧改为success,可以被删除
    if($oldId !==0 && $oldId !== $signId) {
      $one = $this->getInfo(['signId'=>$oldId]);
      if($one === null) {
        $smsBLL->edit(['logicId'=>$oldId], ['status'=>'success']);
      }
    }
    return true;
  }

  function setTpl($place, $tplId) {
    $smsBLL = new SmsBLL();
    // 1.模板不存在或不可用,报错
    $tpl = $smsBLL->getInfo(['logicId'=>$tplId, 'type'=>'tpl']);
    if($tpl === null || $tpl['status']==='pending' || $tpl['status']==='fail') {
      thrower('sms', 'tplNotFound');
    }
    // 2.模板设为使用中,不可被删除
    if($tpl['status'] === 'success') {
      $smsBLL->edit(['logicId'=>$signId], ['status'=>'success']);
    }
    // 3.更改占位短信的模板
    $place = $this->getInfo(['place'=>$place]);
    $oldId = $place['tplId'];
    $this->update(['tplId'=>$tpl['logicId'],'tpl'=>$tpl['text']], ['place'=>$place['place']]);
    // 4.如果旧模板没有被引用,旧改为success,可以被删除
    if($oldId !== 0 && $oldId !== $tplId) {
      $one = $this->getInfo(['tplId'=>$oldId]);
      if($one === null) {
        $smsBLL->edit(['logicId'=>$oldId], ['status'=>'success']);
      }
    }
    return true;
  }
}
?>