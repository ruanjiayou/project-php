<?php
include_once __DIR__.'/utils/CustomRoute.php';
include_once __DIR__.'/utils/Hinter.php';
include_once __DIR__.'/utils/Validater.php';

// function loadClass($opt = array()) {
//   if(!isset($opt['recusive'])) {
//     $opt['recusive'] = false;
//   }
//   if(isset($opt['dir']) && is_dir($opt['dir'])) {
//     $dh = opendir($opt['dir']);
//     while(($file=readdir($dh))!==false) {
//       if($file !='.'&&$file!='..') {
//         $fullpath = $opt['dir'].'/'.$file;
//         if(is_file($fullpath)) {
//           include_once($fullpath);
//         }
//         if($opt === 'true' && is_dir($fullpath)) {
//           $opt2 = [
//             'dir'=>$fullpath,
//             'recusive' => $opt['recusive'],
//             'callback' => function_exists($opt['callback']) ? $opt['callback'] : null];
//           loadClass($opt2);
//         }
//       }
//     }
//     closedir($dh);
//   }
// }
// // 加载所有model
// // CustomRoute::scanner(['dir'=>__DIR__.'/models']);
// loadClass(['dir'=>__DIR__.'/models']);
// 加载所有route
CustomRoute::loadAll(['dir'=>__DIR__.'/routes']);
?>