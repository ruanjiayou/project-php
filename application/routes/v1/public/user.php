<?php
use app\model;
use think\Request;
use think\Response;

return [
  'get /v1/public/users' => function($req, $res) {
    $hql = $req->paging();
    $hql['where']['type'] = 'servant';
    $hql['where']['status'] = 'approved';
    $hql['field'] = '!password,token,salt';
    $query = input('get.');
    if(isset($query['attr'])) {
      if(in_array($query['attr'], ['hot', 'recommend'])) {
        $hql['where']['attr'] = $query['attr'];
      }
    }
    $res->paging(model('user')->getList($hql));
  }
];
?>