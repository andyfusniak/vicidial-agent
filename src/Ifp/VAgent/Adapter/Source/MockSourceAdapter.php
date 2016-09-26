<?php
namespace Ifp\VAgent\Adapter\Source;

use Ifp\VAgent\Resource\Item;
use Ifp\VAgent\Resource\ItemCollection;

class MockSourceAdapter extends SourceAdapterAbstract implements SourceAdapterInterface
{
    private static $mockData = [
        [
            'id'        => 121,
            'firstname' => 'Jack',
            'lastname'  => 'Nicholson',
            'telephone' => '123456789'
        ],
        [
            'id'        => 122,
            'firstname' => 'Robert',
            'lastname'  => 'De Niro',
            'telephone' => '234567890'
        ],
        [
            'id'        => 123,
            'firstname' => 'Al',
            'lastname'  => 'Pacino',
            'telephone' => '345678901'
        ]
    ];

    /**
     * @var ItemCollection
     */
    private $itemCollection;

    public function init()
    {
        $this->itemCollection = new ItemCollection();
        foreach (self::$mockData as $data) {
            $this->itemCollection->add(Item::factory($data));
        }

        $this->totalCount = count(self::$mockData);
        $cursor = 1;
    }

    /**
     * @return int total number of items in the data source
     */
    public function countTotalItems()
    {
        return count($this->itemCollection);
    }
    
    /**
     * @return int current cursor position in the list
     */
    public function getCursorPosition()
    {
        return $this->cursor;
    }

    /**
     * @return ItemCollection
     */
    public function getAllItems()
    {
        return $this->itemCollection;
    }

    /**
     * @return Item
     */
    public function getNextItem()
    {
        $current = $this->itemCollection->current();
        $this->itemCollection->next();
        return $current;
    }

    /**
     * Not implemented 
     * @return Items
     */
    public function getNextPage()
    {
        return null;
    }

    /**
     * @param int $batchSize maximum number of items to pull per page
     */
    public function setPageSize($size)
    {
        $this->page = (int) $size;
    }
}