<?php
use \Firebase\JWT\JWT;
use think\Request;

class UserWorkBLL extends BLL {

  public $table = 'user_work';                                                                                                                                   
  
  public function setWorks($data, $userId) {
    $validation = new Validater([
      'works' => 'required|array'
    ]);
    $input = $validation->validate($data);
    $results = [];
    for($i=0;$i<count($input['works']);$i++) {
      $work = $input['works'][$i];
      $query = ['userId'=>$userId,'workAt'=>$work];
      if($this->isWork($query)) {
        $res = ['id'=>null,'workAt'=>$work];
      } else {
        $res = $this->create($query);
      }
      array_push($results, $res);
    }
    return $results;
  }

  public function create($data) {
    $validation = new Validater([
      'userId' => 'required|int',
      'workAt' => 'required|dateonly',
      'createdAt' => 'required|string|default:datetime'
    ]);
    $input = $validation->validate($data);
    return model($this->table)->add($input);
  }

  public function isWork($data) {
    $validation = new Validater([
      'userId' => 'required|int',
      'workAt' => 'required|dateonly'
    ]);
    $input = $validation->validate($data);
    return null !== model($this->table)->getInfo($input);
  }

  public function getMonthWorks($userId, $input) {
    $validation = new Validater([
      'year' => 'int',
      'month'=> 'int'
    ]);
    $query = $validation->validate($input);
    if(!isset($query['year'])) {
      $query['year'] = date('Y');
    }
    if(!isset($query['month'])) {
      $query['month'] = date('m');
    }
    $where = ['userId'=>$userId, 'workAt'=>['like', $query['year'].'-'.$query['month'].'-'.'%']];
    $result = model($this->table)->getList(['where'=>$where]);
    return $result;
  }

}

?>