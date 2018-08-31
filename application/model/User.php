<?php
namespace app\model;
use think\Model;
class User extends ModelBase {
  function prices() {
    return $this->belongsTo('price', 'userId');
  }
  function pictures() {
    return $this->belongsTo('user_image', 'userId');
  }
}
