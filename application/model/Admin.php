<?php
namespace app\model;
use think\Model;
class Admin extends ModelBase {
  function AdminAuth() {
    return $this->hasMany('admin_auth', 'adminId');
  }
}
