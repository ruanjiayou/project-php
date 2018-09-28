<?php
require __DIR__ . '/../../public/index.php';
use think\Log;
// Log::write('test\\n');
// $fh = fopen('/home/d.txt', 'a+');
// fwrite($fh,'123\n');
// fclose($fh);

/**
 * 自动返现
 * @param $id 邀请订单id
 */
function rebate($id) {
  $invitation = db('invitation')->where(['id'=>$id])->find();
  if(!empty($invitation)) {
    Log::record($invitation);
  } else {
    Log::record('empty');
  }
}

$tasks = db('task')->where(['type' => 'invitation'])->limit(0)->select();
$ids = [];
// success
$invitationBLL = new \InvitationBLL();
Log::write($invitationBLL);
// fail getInfo()不存在?
// $invitation = $invitationBLL->getInfo(33);
// Log::write($invitation);
// fail
// $invitation = new \Invitation();
// Log::write($invitation);
for($i=0;$i<count($tasks);$i++) {
  $task = $tasks[$i];
  array_push($ids, $tasks[$i]['id']);
  if($task['type'] === 'invitation') {
    // rebate($task['taskId']);
  }
}
if(!empty($ids)) {
  db('task')->where(['id'=>['in', $ids]])->delete();
}
