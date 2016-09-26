<?php
namespace Ifp\VAgent\Source;

abstract class SourceAdapaterAbstract implements SourceAdapaterInterface
{
    protected $cursor;

    public function __construct()
    {
        init();
    }
}