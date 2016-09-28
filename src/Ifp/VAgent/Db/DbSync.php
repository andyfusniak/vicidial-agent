<?php
namespace Ifp\VAgent\Db;

class DbSync
{
    const DATA_SYNC_SUCCESS = 'success';
    const DATA_SYNC_ERROR = 'error';

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * Inject the PDO object dependency via the constructor
     *
     * @param \PDO $pdo the vagent database connection object
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the MySQL data source primary key by name
     *
     * @param string $name the name of the data source
     * @return int primary key of the data source row
     */
    public function getDataSourceIdByName(string $name)
    {
        $statement = $this->pdo->prepare('
            SELECT `data_source_id`
            FROM `data_sources`
            WHERE `name` = :name
        ');
        $statement->bindValue(':name', (string) $name, \PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return $row['data_source_id'];
    }

    public function addDataSync($sourceId, $id, $status)
    {
        $statement = $this->pdo->prepare('
            INSERT INTO `data_sync` (
                `data_sync_id`, `source_id`, `id`,
                `status`, `last_sync`, `created`
            ) VALUES (
                null, :source_id, :id,
                :status, NOW(), NOW()
            )
        ');
        $statement->bindValue(
            ':source_id',
            $sourceId,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':id',
            $id,
            \PDO::PARAM_STR
        );
        $statement->bindValue(
            ':status',
            $status,
            \PDO::PARAM_STR
        );
        $statement->execute();
    }

    public function updateDataSync($sourceId, $id, $status)
    {
        $statement = $this->pdo->prepare('
            UPDATE `data_sync`
            SET `status` = :status, `last_sync` = NOW()
            WHERE source_id = :source_id
              AND id = :id
        ');
        $statement->bindValue(
            ':source_id',
            $sourceId,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':id',
            $id,
            \PDO::PARAM_STR
        );
        $statement->bindValue(
            ':status',
            $status,
            \PDO::PARAM_STR  
        );
        $statement->execute();
    }

    public function getSyncStatus($sourceId, $id)
    {
        $statement = $this->pdo->prepare('
            SELECT `status`
            FROM `data_sync`
            WHERE source_id = :source_id
              AND id = :id
        ');
        $statement->bindValue(
            ':source_id',
            $sourceId,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':id',
            $id,
            \PDO::PARAM_STR
        );
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return $row['status'];
        }
        return null;
    }

    public function syncRowExists($sourceId, $id)
    {
        $statement = $this->pdo->prepare('
            SELECT `data_sync_id`
            FROM `data_sync`
            WHERE `source_id` = :source_id
              AND `id` = :id
        ');
        $statement->bindValue(
            ':source_id',
            (int) $sourceId,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':id',
            $id,
            \PDO::PARAM_STR
        );
        $statement->execute();
        if ($statement->rowCount() === 0) {
            return false;
        }

        return true;
    }

    public function updateSourceCursorAndSyncTotals($dataSourceId, $id)
    {
        $statement = $this->pdo->prepare('
            UPDATE `data_sources`
            SET `cursor` = :id,
              sync_success_total = (
                  SELECT COUNT(`data_sync_id`)
                  FROM `data_sync`
                  WHERE `source_id` = :data_source_id
                    AND `status` = "success"
              ),
              sync_error_total = (
                  SELECT COUNT(`data_sync_id`)
                  FROM `data_sync`
                  WHERE `source_id` = :data_source_id
                    AND `status` = "error"
              ),
              modified = NOW()
            WHERE `data_source_id` = :data_source_id
        ');
        $statement->bindValue(
            ':data_source_id',
            (int) $dataSourceId,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':id',
            $id,
            \PDO::PARAM_STR
        );
        $statement->execute();
    }

    public function markDataSync($sourceId, $id, $status)
    {
        $this->pdo->beginTransaction();

        if ($this->syncRowExists($sourceId, $id)) {
            $this->updateDataSync($sourceId, $id, $status);
        } else {
            $this->addDataSync($sourceId, $id, $status);
        }

        $this->updateSourceCursorAndSyncTotals(
            $sourceId,
            $id
        );

        $this->pdo->commit();
    }

    public function addDataLog($params)
    {
        $statement = $this->pdo->prepare('
            INSERT INTO `data_log` (
                `data_log_id`, `source_id`, `action`, `status`,
                `api_call`, `response`, `created`
            ) VALUES (
                null, :source_id, :action, :status,
                :api_call, :response, NOW()
            )
        ');
        $statement->bindValue(
            ':source_id',
            $params['source_id'],
            \PDO::PARAM_STR
        );
        $statement->bindValue(
            ':action',
            $params['action'],
            \PDO::PARAM_STR
        );
        $statement->bindValue(
            ':status',
            $params['status'],
            \PDO::PARAM_STR
        );
        $statement->bindValue(
            ':api_call',
            $params['api_call'],
            \PDO::PARAM_STR
        );
        $statement->bindValue(
            ':response',
            $params['response'],
            \PDO::PARAM_STR
        );
        $statement->execute();
    }

    /**
     * Update the source stats
     *
     * @param int $sourceCountTotal
     * @param int $sourceLastRecord
     */
    public function updateSourceStats($dataSourceId,
                                      $sourceCountTotal,
                                      $sourceLastRecord)
    {
        $statement = $this->pdo->prepare('
            UPDATE `data_sources`
            SET source_count_total = :source_count_total,
                source_last_record = :source_last_record,
                sync_success_total = (
                    SELECT COUNT(`data_sync_id`)
                    FROM `data_sync`
                    WHERE `source_id` = :source_id
                      AND `status` = "success"
                ),
                sync_error_total = (
                    SELECT COUNT(`data_sync_id`)
                    FROM `data_sync`
                    WHERE `source_id` = :source_id
                      AND `status` = "error"
                ),
                modified = NOW()
            WHERE data_source_id = :data_source_id
        ');

        $statement->bindValue(
            ':source_id',
            $dataSourceId,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':source_count_total',
            (int) $sourceCountTotal,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':source_last_record',
            (int) $sourceLastRecord,
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':data_source_id',
            (int) $dataSourceId,
            \PDO::PARAM_INT
        );
        $statement->execute();
    }

    public function disconnect()
    {
        $this->pdo = null;
    }
}