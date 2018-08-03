<?php
return [
  /**
   * @api {post} /v1/admin/admin 添加普通管理员
   * @apiGroup admin-admin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {string} [nickName] 昵称
   * @apiParam {string} [phone] 手机号
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 0,
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/admin/admin' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $admin = $adminBLL::auth($req);

    $nAdmin = $adminBLL->create(input('post.'));
    $res->return($nAdmin);
  },
  /**
   * @api {delete} /v1/admin/admin/:adminId 删除普通管理员
   * @apiGroup admin-admin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/admin/admin/:adminId' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $admin = $adminBLL::auth($req);
    
    $adminBLL->destroy($req->param('adminId'));
    $res->success();
  },
  /**
   * @api {put} /v1/admin/admin/:adminId/authority 修改权限
   * @apiGroup admin-admin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {array} rights 权限数组
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/admin/admin/:adminId/authority' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $admin = $adminBLL::auth($req);
    
    $adminBLL::changeRight($req->param('adminId'), input('put.'));
    $res->success();
  },
  /**
   * @api {get} /v1/admin/admin 普通管理员列表
   * @apiGroup admin-admin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} page 页码
   * @apiParam {int} limit 每页数量默认10
   * @apiParam {string} search 手机号或昵称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   ecode: 0,
   *   error: '',
   *   edata: [{
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 0,
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
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
  'get /v1/admin/admin' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $admin = $adminBLL::auth($req);
    
    $hql = $req->paging(function($h){
      $h['where'] = input('get.');
      $h['where']['isSA'] = 0;
      $h['field'] = '!password,token,salt';
    });
    //TODO: 关联查询 权限表
    $admins = $adminBLL->getList($hql);
    $res->paging($admins);
  },
  /**
   * @api {get} /v1/admin/admin/:adminId 普通管理员详情
   * @apiGroup admin-admin
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {int} page 页码
   * @apiParam {int} limit 每页数量默认10
   * @apiParam {string} search 手机号或昵称
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   ecode: 0,
   *   error: '',
   *   edata: {
   *     id: 1,
   *     phone: '18888888888',
   *     nickName: 'max',
   *     avatar: 'https://images.baidu.com',
   *     isSA: 0,
   *     createdAt: "2018-07-31 17:43:48"
   *   }
   * }
   */
  'get /v1/admin/admin/:adminId' => function($req, $res) {
    $adminBLL = new AdminBLL();
    $admin = $adminBLL::auth($req);
    
    $nAdmin = $adminBLL->getInfo($req->param('adminId'));
    $res->return($nAdmin);
  }
];
?>