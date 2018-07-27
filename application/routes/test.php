<?php
use app\model;
use think\Request;
use think\Response;

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
      throw (new Hinter())->setHinter(['message'=>'test']);
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
   * @api {get} /test/model 10.测试model
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
      $result = $bannerModel->add(['url'=>'test']);
      return $result;
    } catch(Exception $e) {
      dump($e);
      exit;
    }
  }
]
?>