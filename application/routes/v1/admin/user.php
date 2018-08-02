<?php
use app\model;
use think\Request;
use think\Response;

return [
  'get /v1/admin/users' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $hql = $req->paging();
    //TODO: 查询都放到 BLL中
    $validation = new Validater([
      'type' => 'string|enum:servant,buyer,agency',
      'status' => 'string|enum:approving,approved,forbidden',
      'attr' => 'string|enum:normal,hot',
      'search' => 'string'
    ]);
    $query = $validation->validate(input('get.'));
    if(isset($query['type'])) {
      $hql['where']['type'] = $query['type'];
    }
    if(isset($query['status'])) {
      $hql['where']['status'] = $query['status'];
    }
    if(isset($query['attr'])) {
      if(in_array($query['attr'], ['hot', 'recommend'])) {
        $hql['where']['attr'] = $query['attr'];
      }
    }
    if(isset($query['search'])) {
      $hql['where']['phone|nickName'] = ['like', '%'.$query['search'].'%'];
    }
    $hql['field'] = '!password,token,salt';
    $res->paging(model('user')->getList($hql));
  },
  'get /v1/admin/users/:userId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $user = model('user')->getInfo(['id'=>$req->param('userId')]);
    $res->return($user);
  },
  'put /v1/admin/users/:userId' => function($req, $res) {
    $admin = AdminBLL::auth($req);
    $validation = new Validater([
      'status' => 'string|enum:approved,forbidden',
      'attr' => 'string|enum:normal,hot'
    ]);
    $input = $validation->validate(input('put.'));
    $user = UserBLL::update($input, ['id'=>$req->param('userId')]);
    $res->return($user);
  }
];
?>