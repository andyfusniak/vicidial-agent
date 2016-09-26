<?php
namespace Ifp\VAgent\Resource;

use Ifp\VAgent\Resource\Item;

class ItemCollection implements \Iterator, \Countable
{
    /**
     * @var int
     */
    private $cursor;

    /**
     * @var array
     */
    private $items = [];

    public function __construct()
    {
        $this->cursor = 0;
    }

    /**
     * Add a new item to the collection
     * @param Item the object to add
     * return ItemOrderedCollection
     */
    public function add(Item $t)
    {
        array_push($this->items, $t);
        return $this;
    }

    public function count()
    {
        return count($this->items);
    }


    public function current()
    {
        return $this->items[$this->cursor];
    }

    public function key()
    {
        return $this->cursor;
    }

    public function next()
    {
        $this->cursor++;
    }

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function valid()
    {
        return isset($this->items[$this->cursor]);
    }
}