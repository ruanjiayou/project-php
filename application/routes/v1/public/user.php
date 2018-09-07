<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {get} /v1/public/users 获取用户列表
   * @apiGroup public-user
   * 
   * @apiParam {int} [page] 页码
   * @apiParam {int} [limit] 每页数量默认10
   * @apiParam {string} [search] 手机号或昵称
   * @apiParam {string='servant','buyer','agency'} [type] 用户类型
   * @apiParam {string='hot','recommend','normal'} [attr] 属性
   * @apiParam {string} [distance] 按距离排序,如:distance=114.21498,30.58145
   * @apiParam {int} [cityId] 城市id搜索
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   rdata: [{
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
   *     type: "servant",
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
   *   ecode: 0,
   *   error: '',
   *   statck: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 10,
   *     count: 1,
   *     total: 1,
   *   }
   * }
   */
  'get /v1/public/users' => function($req, $res) {
    $userBLL = new UserBLL();

    $hql = $req->paging(function($h) {
      $h['where'] = input('get.');
      $h['where']['type'] = 'servant';
      $h['where']['status'] = 'approved';
      if(isset($h['where']['attr']) && $h['where']['attr']==='hot') {
        $h['order'] = 'popular DESC';
        unset($h['where']['attr']);
      }
      return $h;
    });
    if(isset($_GET['distance']) && preg_match('/^\d+\.\d+[,]\d+.\d+$/', $_GET['distance'])) {
      $count = model('user')->count();
      $data = model('user')->query('select *,(st_distance (point (x, y),point('.$_GET['distance'].')) / 0.0111) AS distance from user where type="servant" and status="approved" order by distance limit '.($hql['page']-1)*$hql['limit'].','.$hql['limit']);//.($hql['page']-1)*$hql['limit'].','.$hql['limit']
      $res->return($data, [
        'paginator' => [
          'total' => $count,
          'count' => count($data),
          'page' => $hql['page'],
          'limit'=> $hql['limit'],
          'pages'=> ceil($count/$hql['limit'])
        ]
      ]);
    } else {
      $res->paging($userBLL->getList($hql));
    }
  },
  /**
   * @api {get} /v1/public/users/:userId 用户详情
   * @apiGroup public-user
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * images: 用户相册
   * prices: 邀请订单价格
   */
  'get /v1/public/users/:userId' => function($req, $res) {
    $userId = $req->param('userId');
    $userBLL = new UserBLL();
    $user = $userBLL->getInfo($userId);
    $user['popular'] += 1;
    $userBLL->update(['popular'=>$user['popular']], $userId);
    $res->return($user);
  },
  /**
   * @api {get} /v1/public/users/:userId/works 用户月工作计划列表
   * @apiGroup public-user
   * 
   * @apiParam {int} [year] 年份
   * @apiParam {int} [month] 月份
   */
  'get /v1/public/users/:userId/works' => function($req, $res) {
    $userWorkBLL = new UserWorkBLL();

    $results = $userWorkBLL->getMonthWorks($req->param('userId'), input('get.'));
    $res->paging($results);
  }
];
?>