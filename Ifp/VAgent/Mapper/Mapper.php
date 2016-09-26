<?php
namespace Ifp\VAgent\Mapper;

use Ifp\VAgent\Mapper\MapperInterface;
use Ifp\VAgent\Source\SourceAdapterInterface as SourceAdapter;
use Ifp\VAgent\Dest\DestAdapterInterface as DestAdapter;

class Mapper implements MapperInterface
{
    /**
     * @var SourceAdapaterInterface
     */
    protected $sourceAdapter;

    /**
     * @bar DestAdapaterInterface
     */
    protecte $destAdapater;

    public function __construct(SourceAdapter $sourceAdapter, DestAdapter $destAdapater)
    {
        $this->sourceAdapter = $sourceAdapter;
        $this->destAdapater = $destAdapater;
    }

    public function process()
    {
        $numSourceItems = $sourceAdapter->countTotalItems();

        while ($sourceAdapter->getNextItem()) {
            var_dump($sourceAdapter);
        }


    }
}