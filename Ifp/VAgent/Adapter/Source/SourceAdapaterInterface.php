<?php
namespace Ifp\VAgent\Source;

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
     * @return Items
     */
    public function getAllItems();

    /**
     * @return Item
     */
    public function getNextItem();

    /**
     * @return Items
     */
    public function getNextPage();

    /**
     * @param int $batchSize maximum number of items to pull per page
     */
    public function setPageSize($size);
}