<?php
use \Firebase\JWT\JWT;
use think\Request;

class RccodeBLL extends BLL {
  
  public $table = 'rccode';

  public function create($agency) {
    $found = false;
    $rccode = '';
    do {
      $rccode = _::random(6, 'imix');
      $d = model($this->table)->getInfo(['rccode'=>$rccode]);
      $found = empty($d) ? false : true;
    } while($found);
    return model($this->table)->add(['agencyId'=>$agency['id'], 'agencyName'=>$agency['nickName'], 'agencyAvatar'=>$agency['avatar'], 'rccode'=>$rccode, 'createdAt'=>date('Y-m-d H:i:s')]);
  }

}

?>