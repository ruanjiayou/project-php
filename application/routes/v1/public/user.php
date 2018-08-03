<?php
use app\model;
use think\Request;
use think\Response;

return [
  'get /v1/public/users' => function($req, $res) {
    $userBLL = new UserBLL();

    $hql = $req->paging(function($h) {
      $h['where'] = input('get.');
      $h['where']['type'] = 'servant';
      $h['where']['status'] = 'approved';
      return $h;
    });
    $res->paging($userBLL->getList($hql));
  }
];
?>