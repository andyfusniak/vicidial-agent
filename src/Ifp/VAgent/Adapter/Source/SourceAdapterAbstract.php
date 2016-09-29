<?php
namespace Ifp\VAgent\Adapter\Source;

abstract class SourceAdapterAbstract implements SourceAdapterInterface
{
    /**
     * @var int
     */
    protected $cursor;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $pageSize = 100;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->page = 0;
        $this->cursor = null;
    }

    /**
     * Set the max number of items in a page
     *
     * @param int $pageSize the page size in number of items
     * @return SourceAdapterInterface
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = (int) $pageSize;
        return $this;
    }
}