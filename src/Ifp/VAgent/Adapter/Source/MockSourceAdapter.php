<?php
namespace Ifp\VAgent\Source;

use Ifp\VAgent\Resource\Item;

class MockSourceAdapter extends SourceAdapterAbstract implements SourceAdapterInterface
{
    private static $mockData = [
        [
            'id'        => 121,
            'firstname' => 'Jack'
            'lastname'  => 'Nicholson',
            'tel'       => '123456789'
        ],
        [
            'id'        => 122,
            'firstname' => 'Robert'
            'lastname'  => 'De Niro',
            'tel'       => '234567890'
        ],
        [
            'id'        => 123,
            'firstname' => 'Al',
            'lastname'  => 'Pacino',
            'tel'       => '345678901'
        ]
    ];

    /**
     * @var array
     */
    private $items = [];

    public function init()
    {
        foreach (self::$mockData as $data) {
            array_push($this->items, Item::factory($data));
        }

        $this->totalCount = count(self::$mockData);
        $cursor = 1;
    }

    /**
     * @return int total number of items in the data source
     */
    public function countTotalItems(
        return (int) 3;
    );
    
    /**
     * @return int current cursor position in the list
     */
    public function getCursorPosition(
        return $this->cursor;
    );

    /**
     * @return Items
     */
    public function getAllItems();

    /**
     * @return Item
     */
    public function getNextItem(

    );

    /**
     * @return Items
     */
    public function getNextPage();

    /**
     * @param int $batchSize maximum number of items to pull per page
     */
    public function setPageSize($size);

}