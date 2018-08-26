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
      'text' => 'required|string',
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
      $result = model($this->table)->add(_::pick($data, ['logicId', 'text', 'type', 'status', 'createdAt', 'description']));
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
      $result = wxHelper::delSmsSign([intval($sign['logicId'])]);
      if($result['result']!==0) {
        thrower('common', 'thirdApiFail', $result['result'].' '.$result['msg']);
      }
      return $signModel->destroy($query);
    }
  }
  /**
   * 此方法禁用
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
   * 获取所有签名并刷新审核状态
   */
  function getSign() {
    $results = model($this->table)->getList(['where'=>['type'=>'sign'], 'limit'=>0]);
    $ids = [];
    for($n=0;$n<count($results);$n++) {
      if($results[$n]['status'] === 'pending') {
        array_push($ids, intval($results[$n]['logicId']));
      }
    }
    $signs = [];
    if(count($ids)!==0) {
      $signs = wxHelper::getSmsSign($ids);
    }
    $res = [];
    for($i=0;$i<count($results);$i++) {
      $result = $results[$i];
      for($j=0;$j<count($signs);$j++) {
        $sign = $signs[$j];
        if($sign['id'] === intval($result['logicId'])) {
          if($sign['status'] === 0) {
            $result = model($this->table)->edit(['id'=>$result['id']], ['status'=>'success']);
            
          } elseif($sign['status'] === 2) {
            $result = model($this->table)->edit(['id'=>$result['id']], ['status'=>'fail', 'reason'=>$sign['reply']]);
          }
          break;
        }
      }
      array_push($res, $result);
    }
    return $res;
  }
  /**
   * 添加模板
   */
  function addTpl($input) {
    $tpl = wxHelper::addSmsTpl($input);
    $result = null;
    if($tpl['result'] === 0) {
      $result = model($this->table)->add([
        'logicId' => $tpl['data']['id'],
        'text' => $input['text'],
        'type' => 'common',
        'status' => 'pending',
        'createdAt' => date('Y-m-d H:i:s')
      ]);
    } else {
      thrower('sms', 'addTplFail', $tpl['result'].' '.$tpl['msg']);
    }
    return $result;
  }

  /**
   * 删除模板
   */
  function delTpl($smsId) {
    $smsModel = model($this->table);
    $query = [$smsModel->primaryKey=>$smsId, 'type'=>'common'];
    $tpl = $smsModel->getInfo($query);
    if($tpl !== null && $tpl['status'] === 'using') {
      thrower('sms', 'smsSignUsing');
    } else {
      // 删除数据库的同事 同步 微信平台
      $result = wxHelper::delSmsTpl([intval($tpl['logicId'])]);
      if($result['result']!==0) {
        thrower('common', 'thirdApiFail', $result['result'].' '.$result['msg']);
      }
      return $smsModel->destroy($query);
    }
  }
  /**
   * 获取所有目标并刷新审核状态
   */
  function getTpl($hql) {
    $dataset = model($this->table)->getList($hql);
    $results = $dataset->items();
    $ids = [];
    for($n=0;$n<count($results);$n++) {
      if($results[$n]['status'] === 'pending') {
        array_push($ids, intval($results[$n]['logicId']));
      }
    }
    $tpls = [];
    if(count($ids)!==0) {
      $tpls = wxHelper::getSmsTpl($ids);
    }
    $paginator = [
      R_PAGENATOR_PAGE =>$dataset->currentPage(),
      R_PAGENATOR_PAGES =>$dataset->lastPage(),
      R_PAGENATOR_LIMIT =>$dataset->listRows(),
      R_PAGENATOR_COUNT =>$dataset->count(),
      R_PAGENATOR_TOTAL=>$dataset->total(),
    ];
    $res = [];
    for($i=0;$i<count($results);$i++) {
      $result = $results[$i];
      for($j=0;$j<count($tpls);$j++) {
        $tpl = $tpls[$j];
        if($tpl['id'] === intval($result['logicId'])) {
          if($tpl['status'] === 0) {
            $result = model($this->table)->edit(['id'=>$result['id']], ['status'=>'success']);
            
          } elseif($tpl['status'] === 2) {
            $result = model($this->table)->edit(['id'=>$result['id']], ['status'=>'fail', 'reason'=>$tpl['reply']]);
          }
          break;
        }
      }
      array_push($res, $result);
    }
    return ['data'=>$res, R_PAGENATOR=>$paginator];
  }
}
?>