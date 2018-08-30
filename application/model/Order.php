<?php
namespace app\model;
use think\Model;
class Order extends ModelBase {
  function User() {
    return $this->BelongsTo('user', 'userId');
  }
}
