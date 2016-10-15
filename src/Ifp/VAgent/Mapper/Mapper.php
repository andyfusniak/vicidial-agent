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

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Logger
     */
    protected $log;

    public function __construct(DbSync $dbSync,
                                SourceAdapter $sourceAdapter,
                                DestAdapter $destAdapater,
                                $options = null)
    {
        $this->dbSync = $dbSync;
        $this->sourceAdapter = $sourceAdapter;
        $this->destAdapater = $destAdapater;
        if ($options) {
            if (isset($options['skip_errors'])) {
                $this->options['skip_errors'] = $options['skip_errors'];
            } else {
                // default
                $this->options['skip_errors'] = false;
            }

            if (isset($options['dry_run_mode'])) {
                $this->options['dry_run_mode'] = $options['dry_run_mode'];
            } else {
                $this->options['dry_run_mode'] = false;
            }
        }
    }

    public function process()
    {
        $dataSourceName = $this->sourceAdapter->getName();
        $dataSourceId = $this->dbSync->getDataSourceIdByName(
            $dataSourceName
        );
        
        if (null === $dataSourceId) {
            if ($this->log) {
                $this->log->debug(sprintf(
                    'Cannot find sync data row for %s',
                    $dataSourceName 
                ));
            }
            return;
        }

        $this->dbSync->updateSourceStats(
            $dataSourceId,
            $this->sourceAdapter->countTotalItems(),
            $this->sourceAdapter->getLastRecordId()
        );
    
        while ($items = $this->sourceAdapter->getNextPage()) {
            foreach ($items as $item) {
                $status = $this->dbSync->getSyncStatus($dataSourceId, $item->getId());

                if (DbSync::DATA_SYNC_SUCCESS === $status) {
                    if ($this->log) {
                        $this->log->debug('Skipping data source as it has already been processed before', [$dataSourceId, $item->getId()]);
                    }
                    continue;
                } else if ((DbSync::DATA_SYNC_ERROR === $status)
                    && (true === $this->options['skip_errors'])) {
                    if ($this->log) {
                        $this->log->debug('Skipping data source as it has already before', [$dataSourceId, $item->getId()]);
                    }
                    continue;
                }

                if (false === $this->options['dry_run_mode']) {
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
    }

    /**
     * Inject the Logger dependency (optional)
     *
     * @param Logger
     * @return VicidialDestAdapter
     */
    public function setLogger($logger)
    {
        $this->log = $logger;
        return $this;
    }
}
