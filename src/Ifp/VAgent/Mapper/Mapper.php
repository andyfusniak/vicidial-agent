<?php
namespace Ifp\VAgent\Mapper;

use Ifp\VAgent\Mapper\MapperInterface;
use Ifp\VAgent\Db\DbSync;
use Ifp\VAgent\Adapter\Source\SourceAdapterInterface as SourceAdapter;
use Ifp\VAgent\Adapter\Dest\DestAdapterInterface as DestAdapter;

class Mapper implements MapperInterface
{
    /**
     * @var DbSync
     */
    protected $dbSync;

    /**
     * @var SourceAdapaterInterface
     */
    protected $sourceAdapter;

    /**
     * @var DestAdapaterInterface
     */
    protected $destAdapater;

    public function __construct(DbSync $dbSync,
                                SourceAdapter $sourceAdapter,
                                DestAdapter $destAdapater)
    {
        $this->dbSync = $dbSync;
        $this->sourceAdapter = $sourceAdapter;
        $this->destAdapater = $destAdapater;
    }

    public function process()
    {
        $dataSourceId = $this->dbSync->getDataSourceIdByName('360');

        $this->dbSync->updateSourceStats(
            $dataSourceId,
            $this->sourceAdapter->countTotalItems(),
            $this->sourceAdapter->getLastRecordId()
        );

        while ($item = $this->sourceAdapter->getNextItem()) {

            if (DbSync::DATA_SYNC_SUCCESS === $this->dbSync->getSyncStatus($dataSourceId, $item->getId())) {
                // already synced so skip
                var_dump("Skipping dataSourceId=" . $dataSourceId . ", id=" . $item->getId());
                continue;
            }

            $result = $this->destAdapater->pushItem($item);
            if (true === $result) {
                $this->dbSync->markDataSync(
                    $dataSourceId,
                    $item->getId(),
                    DbSync::DATA_SYNC_SUCCESS
                );
            } else {
                $this->dbSync->markDataSync(
                    $dataSourceId,
                    $item->getId(),
                    DbSync::DATA_SYNC_ERROR
                );
            }
        }
    }
}