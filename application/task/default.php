<?php
require __DIR__ . '/../../public/index.php';
use think\Log;
use app\model;
// Log::write('test\\n');

$invitations = (new TaskBLL())->getList(['limit' => 0, 'where' => ['type' => 'invitation']]);
$ids = [];
for($i=0;$i<count($invitations);$i++) {
  Log::record($invitations[$i]);
  array_push($ids, $invitations[$i]['id']);
}
Log::write($ids);

// $fh = fopen('/home/d.txt', 'a+');
// fwrite($fh,'123\n');
// fclose($fh);
