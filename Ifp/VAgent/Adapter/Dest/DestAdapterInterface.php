<?php
namespace Ifp\Adapter\Dest;

interface DestAdapaterInterface
{
    public function pushItem(Item $item);
    public function getItem(string $id);
}