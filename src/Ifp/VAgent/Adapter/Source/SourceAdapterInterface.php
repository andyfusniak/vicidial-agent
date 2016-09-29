<?php
namespace Ifp\VAgent\Adapter\Source;

interface SourceAdapterInterface
{
    /**
     * Initialise the adapter
     */
    public function init();

    /**
     * @return int total number of items in the data source
     */
    public function countTotalItems();
    
    /**
     * @return int current cursor position in the list
     */
    public function getCursorPosition();

    /**
     * @return ItemCollection
     */
    public function getAllItems();

    /**
     * @return Item
     */
    public function getNextItem();

    /**
     * @return ItemCollection
     */
    public function getNextPage();

    /**
     * @param int $pageSize maximum number of items to pull per page
     * @return SourceAdapterInterface
     */
    public function setPageSize($pageSize);
}