<?php
namespace app\model;
use think\Model;
class User extends ModelBase {
  function pictures() {
    return $this->belongsTo('user_image', 'userId');
  }
}
