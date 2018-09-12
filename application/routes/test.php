<?php
use app\model;
use think\Request;
use think\Response;

function IGtNotificationTemplateDemo($APPID, $APPKEY){
  $template =  new IGtNotificationTemplate();
  $template->set_appId($APPID);                   //应用appid
  $template->set_appkey($APPKEY);                 //应用appkey
  $template->set_transmissionType(1);            //透传消息类型
  $template->set_transmissionContent("测试离线");//透传内容
  $template->set_title("请填写通知标题");      //通知栏标题
  $template->set_text("请填写通知内容");     //通知栏内容
  $template->set_logo("");                       //通知栏logo
  $template->set_logoURL("");                    //通知栏logo链接
  $template->set_isRing(true);                   //是否响铃
  $template->set_isVibrate(true);                //是否震动
  $template->set_isClearable(true);              //通知栏是否可清除

  return $template;
}

return [
  /**
   * @api {get} /test 1.测试返回字符串
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * 'Hello World!'
   */
  'get /test' => function($req, $res) {
    // dump(input('get.'));
    // dump(input('put.'));
    // dump(input('post.'));
    // dump(input('delete.'));
    return 'Hello World!';
  },
  /**
   * @api {get} /test1 2.测试返回对象
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   name: 'test'
   * }
   */
  'get /test1' => function($req, $res) {
    return ['name'=>'test'];
  },
  /**
   * @api {get} /test/req/paging 3.测试处理查询参数
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   page: 1,
   *   limit: 2,
   *   order: 'id DESC',
   *   search: '搜索'
   * }
   */
  'get /test/req/paging' => function($req, $res) {
    $result = $req->paging();
    $res->return($req->paging());
  },
  /**
   * @api {get} /test/res/return 4.测试return()响应方法
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * ['test']
   */
  'get /test/res/return' => function($req, $res) {
    $res->return(['test']);
  },
  /**
   * @api {get} /test/res/success 5.测试success()响应方法
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'success'
   * }
   */
  'get /test/res/success' => function($req, $res) {
    $res->success();
  },
  /**
   * @api {get} /test/res/fail 6.测试fail()响应方法
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'fail'
   * }
   */
  'get /test/res/fail' => function($req, $res) {
    $res->fail();
  },
  /**
   * @api {get} /test/res/paging 7.测试paging()响应方法
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK 分页
   * {
   *   status: 'success',
   *   result: [],
   *   paging: {
   *     page: 1,
   *     pages: 1,
   *     count: 1,
   *     total: 1,
   *     limit: 1
   *   }
   * }
   * HTTP/1.1 200 OK 全部
   * {
   *   status: 'success',
   *   result: [],
   *   paging: {
   *     page: 1,
   *     pages: 1,
   *     count: 1,
   *     total: 1,
   *     limit: 1
   *   }
   * }
   */
  'get /test/res/paging' => function($req, $res) {
    $res->paging(['test']);
  },
  /**
   * @api {get} /test/hinter 8.测试自定义错误类
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'fail',
   *   data: null,
   *   error: '',
   *   stack: []
   * }
   */
  'get /test/hinter' => function($req, $res) {
    try {
      throw (new Hinter())->setHinter(['message'=>'test'], null);
      //throw new Exception('??');
    } catch(Hinter $h) {
      return $h->info;
    } catch(Exception $e) {
      dump($e);
      exit;
    }
  },
  /**
   * @api {post} /test/validater 9.测试自定义验证器
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   status: 'fail',
   *   data: null,
   *   error: '',
   *   stack: []
   * }
   */
  'post /test/validater' => function($req, $res) {
    try {
      $validation = new Validater([
        'name' => 'required|string|minlength:6|maxlength:18',
        'age' => 'required|int|min:0|max:100',
        'price' => 'required|float:10,2|min:10|max:50',
        'status' => 'nullable|string|enum:pending,success,fail|default:"pending"',
        'dpt' => 'empty|text|default:""|alias:member_%',
        'images' => 'required|array|minlength:1|maxlength:9|default:(toString)',
        'createdAt' => 'int|default:timestamp'
      ]);
      $input = $validation->validate(input('post.'));
      return $input;
    } catch(Hinter $h) {
      return $h->info;
    } catch(Exception $e) {
      dump($e);
      exit;
    }
  },
  /**
   * @api {get} /test/model a.测试model
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   id: 8,
   *   url: 'abc',
   *   sort: 1
   * }
   */
  'get /test/model' => function($req, $res) {
    try {
      $bannerModel = model('banner');
      //$result = $bannerModel->add(['url'=>'test']);
      //return $result;
      $result = $bannerModel->getList(['field'=>'!id']);
      $res->paging($result);
    } catch(Exception $e) {
      dump($e);
      exit;
    }
  },
  /**
   * @api {get} /test/model b.测试thrower
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'fail'
   * }
   */
  'get /test/thrower' => function($req, $res) {
    try {
      thrower('common', 'unknown');
    } catch(Hinter $h) {
      return $h->info;
    }
  },
  /**
   * @api {get} /test/auth c.测试 鉴权
   * @apiGroup test
   * 
   * @apiHeader {string} token 令牌
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'fail'
   * }
   */
  'get /test/auth' => function($req, $res) {
    $req->auth('user');
    return 'auth';
  },
  /**
   * @api {get} /test/_ d.仿lodash函数工具库
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success'
   * }
   */
  'get /test/_' => function($req, $res) {
    _::log('true', _::type(true));
    _::log('[]', _::type([]));
    _::log('new stdClass()', _::type(new stdClass()));
    _::log('function(){}', _::type(function(){}));
    _::log('["test"=>"test"]', _::type(['test'=>'test']));
    _::log('123', _::type(123));
    _::log('123.456', _::type(123.456));
    _::log('string', _::type('string'));
    _::log('null', _::type(null));

    _::log('null', _::isEmptyObject(null));
    _::log('[]', _::isEmptyObject([]));
    _::log('["abc"]', _::isEmptyObject(['abc']));
    dump(strtotime('2018-08-01 17:19:07'));
    return 'end';
  },
  /**
   * @api {get} /test/shttp/get e.测试shttp::get()
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * Hello World
   */
  'get /test/shttp/get' => function($req, $res) {
    $url = 'http://'.$_SERVER['HTTP_HOST'].'/test?a=b';
    return shttp::get($url)->end('string');
  },
  /**
   * @api {get} /test/shttp/post f.测试shttp::post()
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   name: 'test'
   * }
   */
  'post /test/shttp/post' => function($req, $res) {
    $input = input('post.');
    $res->return($input);
  },
  /**
   * @api {get} /test/route-pattern g.测试路由中写正则
   * @apiGroup test
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   name: 'test'
   */
  'get /test/route-pattern' => function($req, $res) {
    $r = CustomRoute::getRoutePattern('get /admin/:adminId([0-9]+)/auth/:name(\w+)');
    $res->return($r);
  },
  /**
   * @api {post} /test/getui 测试个推接口推送APP消息
   * @apiGroup test
   */
  'post /test/getui' => function($req, $res) {
    require_once(EXTEND_PATH . 'GETUI/IGt.Push.php');
    require_once(EXTEND_PATH . 'GETUI/igetui/IGt.AppMessage.php');
    require_once(EXTEND_PATH . 'GETUI/igetui/IGt.APNPayload.php');
    require_once(EXTEND_PATH . 'GETUI/igetui/template/IGt.BaseTemplate.php');
    require_once(EXTEND_PATH . 'GETUI/IGt.Batch.php');
    require_once(EXTEND_PATH . 'GETUI/igetui/utils/AppConditions.php');

    // $HOST = 'http://118.24.248.160:9001';
    $HOST = 'http://sdk.open.api.igexin.com/apiex.htm';
    $APPID = '97El3h2UeD8BODhvS1OH7A';
    $APPKEY = 'OCfGyJKYxX54QCIUr5WX94';
    $MASTERSECRET = 'RwRIayE4C58LeyrIG2JN9A';

    $igt = new IGeTui($HOST,$APPKEY,$MASTERSECRET);
    //定义透传模板，设置透传内容，和收到消息是否立即启动启用
    $template = IGtNotificationTemplateDemo();
    //$template = IGtLinkTemplateDemo();
    // 定义"AppMessage"类型消息对象，设置消息内容模板、发送的目标App列表、是否支持离线发送、以及离线消息有效期(单位毫秒)
    $message = new IGtAppMessage();
    $message->set_isOffline(true);
    $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
    $message->set_data($template);

    $appIdList=array($APPID);
    $phoneTypeList=array('ANDROID');
    $provinceList=array('浙江');
    $tagList=array('haha');
    //用户属性
    //$age = array("0000", "0010");


    //$cdt = new AppConditions();
   // $cdt->addCondition(AppConditions::PHONE_TYPE, $phoneTypeList);
   // $cdt->addCondition(AppConditions::REGION, $provinceList);
    //$cdt->addCondition(AppConditions::TAG, $tagList);
    //$cdt->addCondition("age", $age);

    $message->set_appIdList($appIdList);
    //$message->set_conditions($cdt->getCondition());

    $rep = $igt->pushMessageToApp($message,"任务组名");

    return $rep;
  }
]
?>