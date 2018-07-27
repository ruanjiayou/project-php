<?php
include_once __DIR__.'/utils/CustomRoute.php';
include_once __DIR__.'/utils/Hinter.php';
include_once __DIR__.'/utils/Validater.php';
CustomRoute::loadAll(['dir'=>__DIR__.'/routes']);
?>