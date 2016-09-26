<?php
namespace Ifp\VAgent\Adapter\Dest;

use Ifp\VAgent\Resource\Item;

class VicidialDestAdapter implements DestAdapterInterface
{
    /**
     * Push an item to the VICIdial API using the VicidialApiGateway PHP library
     * @param Item $item to push
     */
    public function pushItem(Item $item)
    {
        var_dump("Pushing to VicidialApiGateway");
        return new Item();
    }
    
    /**
     * Currently returns an empty Item object
     *
     * @todo not yet implemented
     */
    public function getItem(string $id)
    {
        return new Item();
    }
}