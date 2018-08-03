<?php
use app\model;
use think\Request;
use think\Response;

return [
  'get /v1/admin/users' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();
    
    $hql = $req->paging(function($h){
      $h['where'] = input('get.');
      return $h;
    });
    $res->paging($userBLL->getList($hql));
  },
  'get /v1/admin/users/:userId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();

    $user = $userBLL->getInfo($req->param('userId'));
    $res->return($user);
  },
  'put /v1/admin/users/:userId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $userBLL = new UserBLL();

    $user = $userBLL->update(_::pick(input('put.'), ['status', 'attr']), ['id'=>$req->param('userId')]);
    $res->return($user);
  }
];
?>