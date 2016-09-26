<?php
namespace Ifp\VAgent\Mapper;

use Ifp\VAgent\Mapper\MapperInterface;
use Ifp\VAgent\Adapter\Source\SourceAdapterInterface as SourceAdapter;
use Ifp\VAgent\Adapter\Dest\DestAdapterInterface as DestAdapter;

class Mapper implements MapperInterface
{
    /**
     * @var SourceAdapaterInterface
     */
    protected $sourceAdapter;

    /**
     * @bar DestAdapaterInterface
     */
    protected $destAdapater;

    public function __construct(SourceAdapter $sourceAdapter, DestAdapter $destAdapater)
    {
        $this->sourceAdapter = $sourceAdapter;
        $this->destAdapater = $destAdapater;
    }

    public function process()
    {
        $numSourceItems = $this->sourceAdapter->countTotalItems();
        
        while ($item = $this->sourceAdapter->getNextItem()) {
            $this->destAdapater->pushItem($item);
        }

    }
}