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
    $date = date('Y-m-d');
    for($i=0;$i<count($input['works']);$i++) {
      $work = $input['works'][$i];
      $query = ['userId'=>$userId,'workAt'=>$work];
      if($date == $work) {
        (new UserBLL())->update(['isWorkDay'=>1], ['id'=>$userId]);
      }
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
  
  public function destroy($condition) {
    $t = strtotime($condition['workAt']);
    $deathline = strtotime(date('Y-m-d').' 00:00:00');
    if($t < $deathline) {
      return false;
    }
    $work = $this->getInfo($condition);
    if($work !== null) {
      model($this->table)->remove($work['id']);
      $date = date('Y-m-d');
      list($workDate) = explode(' ', $work['workAt']);
      if($date == $workDate) {
        (new UserBLL())->update(['isWorkDay'=>0], $condition['userId']);
      }
      return true;
    }
    return false;
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
      'month'=> 'int',
      'limit'=> 'int|default:"31"'
    ]);
    $query = $validation->validate($input);
    if(!isset($query['year'])) {
      $query['year'] = date('Y');
    }
    if(!isset($query['month'])) {
      $query['month'] = date('m');
    }
    $query['month'] = strlen($query['month']) < 2 ? '0'.$query['month'] : $query['month'];
    $complex = [['like', $query['year'].'-'.$query['month'].'-'.'%'],['>=',date('Y-m-d').' 00:00:00']];
    $where = ['userId'=>$userId, 'workAt'=>$complex];
    $result = model($this->table)->getList(['where'=>$where, 'limit'=>0]);
    return $result;
  }
}

?>