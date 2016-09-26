<?php
namespace Ifp\VAgent\Mapper;

use Ifp\VAgent\Adapter\Source\SourceAdapterInterface as SourceAdapter;
use Ifp\VAgent\Adapter\Dest\DestAdapterInterface as DestAdapter;

interface MapperInterface
{
    public function __construct(SourceAdapter $sourceAdapter, DestAdapter $destAdapater);
}