<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/user/self 修改个人资料
   * @apiGroup user-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} [trueName] 真实姓名
   * @apiParam {string} [alipay] 支付宝账号
   * @apiParam {string} [creditCard] 银行卡
   * @apiParam {string} [nickName] 昵称
   * @apiParam {string} [avatar] 头像
   * @apiParam {int} [age] 年龄
   * @apiParam {int} [height] 身高
   * @apiParam {int} [weight] 体重
   * @apiParam {float} [x] 经度
   * @apiParam {float} [y] 纬度
   * @apiParam {string} [address] 籍贯
   * @apiParam {string} [city] 所在城市
   * @apiParam {int} [cityId] 所在城市
   * @apiParam {string} [identity] 身份证
   * @apiParam {string} [introduce] 简介
   * @apiParam {array} [tags] 个性标签
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     identity: '421224199311111111',
   *     rccode: '123456',
   *     trueName: '阮家友',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     introduce: '简介',
   *     tags: '',
   *     height: 160,
   *     weight: 100,
   *     score: 4.9,
   *     x: "0.0000",
   *     y: "0.0000",
   *     images: 6,
   *     popular: 10086,
   *     money: 888,
   *     address: "",
   *     city: '武汉',
   *     cityId: 77,
   *     type: "servant",
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/user/self' => function($req, $res) {
    $userBLL = new UserBLL();
    $user = $userBLL->auth($req);

    $data = input('post.');
    unset($data['status']);
    if($user['status'] === 'registered' || $user['status'] === 'refused') {
      $data['status'] = $user['type'] === 'servant' ? 'approving' : 'approved';
    }
    $result = $userBLL->update(_::filter($data, ['money', 'attr', 'images']), $user['id']);
    $res->return($result);
  },
  /**
   * @api {post} /v1/user/password 修改密码
   * @apiGroup user-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} newPassword 新密码
   * @apiParam {string} oldPassword 旧密码
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/user/password' => function($req, $res) {
    $userBLL = new UserBLL();
    $user = $userBLL->auth($req);

    $userBLL->changePassword($user, input('post.'));
    $res->success();
  },
  /**
   * @api {get} /v1/user/self 获取个人资料
   * @apiGroup user-self
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: {
   *     id: 1,
   *     phone: '18888888888',
   *     identity: '421224199311111111',
   *     rccode: '123456',
   *     trueName: '阮家友',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     introduce: '简介',
   *     tags: '',
   *     height: 160,
   *     weight: 100,
   *     score: 4.9,
   *     x: "0.0000",
   *     y: "0.0000",
   *     images: 6,
   *     popular: 10086,
   *     money: 888,
   *     address: "",
   *     city: '武汉',
   *     cityId: 77,
   *     type: "servant",
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/user/self' => function($req, $res) {
    $userBLL = new UserBLL();
    $user = $userBLL->auth($req);
    $user = $userBLL->getInfo($user['id']);
    $res->return($user);
  }
];

?>