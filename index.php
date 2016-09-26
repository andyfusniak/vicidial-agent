<?php
require_once './vendor/autoload.php';

$source = new VAgent\Adapter\Source\MysqlSourceAdapter();
$dest = new VAgent\Adapter\Dest\VicidialDestAdapter();

$mapper = new Mapper($source, $dest);


$mapper->process();
