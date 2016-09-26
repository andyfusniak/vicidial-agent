<?php
namespace Ifp\VAgent\Adapter\Dest;

use Ifp\VAgent\Resource\Item;

interface DestAdapterInterface
{
    public function pushItem(Item $item);
    public function getItem(string $id);
}