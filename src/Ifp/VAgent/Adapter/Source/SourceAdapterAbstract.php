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

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->cursor = null;
    }
}