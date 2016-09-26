<?php
require_once './config.php';

$source = new VAgent\Adapter\Source\MysqlSourceAdapter();
$dest = new VAgent\Adapter\Dest\VicidialDestAdapter();

$mapper = new Mapper($source, $dest);


$mapper->process();
