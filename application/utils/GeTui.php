<?php
require_once(EXTEND_PATH . 'GETUI/IGt.Push.php');
require_once(EXTEND_PATH . 'GETUI/igetui/IGt.AppMessage.php');
require_once(EXTEND_PATH . 'GETUI/igetui/IGt.APNPayload.php');
require_once(EXTEND_PATH . 'GETUI/igetui/template/IGt.BaseTemplate.php');
require_once(EXTEND_PATH . 'GETUI/IGt.Batch.php');
require_once(EXTEND_PATH . 'GETUI/igetui/utils/AppConditions.php');

class GeTui {
  public $HOST = 'http://sdk.open.api.igexin.com/apiex.htm';
  public $APPID = '4EU4KO3wS16V0AAiBLoqe5';
  public $APPKEY = 'aA9AVlRWMF6YLK0W7DwJ89';
  public $SECRET = 'xLq0YRx8Sb5VrksjXcFk97';
  public $MASTERSECRET = 'X275tWu7zwAbII4mTIByy9';
  public $igt = null;

  function __construct() {
    $this->igt = new IGeTui($this->HOST, $this->APPKEY, $this->MASTERSECRET);
  }

  function sendMass() {
    
  }
  /**
   * 指定用户发送消息
   * @param [$phone=null] 手机号(用作别名)
   * @param $cid clientId
   * @param $data 消息内容
   */
  function sendOne($phone, $cid, $data) {
    //消息模版
    // 测试IOS APNs透传
    $template =  new IGtTransmissionTemplate();
    //应用appid
    $template->set_appId($this->APPID);
    //应用appkey
    $template->set_appkey($this->APPKEY);
    //透传消息类型
    $template->set_transmissionType(1);
    //透传内容
    $template->set_transmissionContent($data['payload']);
    // APN推送
    // 简单推送

    //高级推送
    $apn = new IGtAPNPayload();
    $alertmsg = new DictionaryAlertMsg();
    $alertmsg->body = $data['content'];
    $alertmsg->actionLocKey="ActionLockey";
    $alertmsg->locKey="LocKey";
    $alertmsg->locArgs=array("locargs");
    $alertmsg->launchImage="launchimage";
    // IOS8.2支持
    $alertmsg->title=$data['title'];
    $alertmsg->titleLocKey="TitleLocKey";
    $alertmsg->titleLocArgs=array("TitleLocArg");
    $apn->alertMsg=$alertmsg;
    // $apn->badge=7;
    $apn->sound="default";
    //$apn->add_customMsg("payload","payload");
    $apn->contentAvailable=1;
    $apn->category="ACTIONABLE";
    $template->set_apnInfo($apn);

    // 设置接收方
    $igtTarget = new IGtTarget();
    $igtTarget->set_appId($this->APPID);
    $igtTarget->set_clientId($cid);
    //$igtTarget->set_alias($phone);
    // 单个消息
    $igtMessage = new IGtSingleMessage();
    $igtMessage->set_isOffline(true);
    $igtMessage->set_data($template);
    return $this->igt->pushMessageToSingle($igtMessage, $igtTarget);
  }
  function setBadge($cid, $n) {
    return $this->igt->setBadgeForDeviceToken($n , $this->APPID, [$cid]);
  }
  function getPushResult($taskId) {
    return $this->igt->getPushResult($taskId);
  }
}

?>