<?php
require_once './vendor/autoload.php';

use Ifp\VAgent\Adapter\Source;
use Ifp\VAgent\Adapter\Dest\VicidialDestAdapter;
use Ifp\VAgent\Mapper\Mapper;

$source = new Source\MockSourceAdapter();

$dest = new VicidialDestAdapter();

$mapper = new Mapper($source, $dest);
$mapper->process();