<?php
namespace Ifp\VAgent\Db;

class DbSync
{
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

    public function addDataSync($params)
    {
        $statement = $this->pdo->prepare('
            INSERT INTO `data_sync` (
                `data_sync_id, `source_id`, `status`, `last_synced`, `created`
            ) VALUES (
                null, :source_id, :status, :last_synced, NOW()
            )
        ');
        $statement->bindValue(
            ':source_id',
            $params['source_id'],
            \PDO::PARAM_INT
        );
        $statement->bindValue(
            ':status',
            $params['status'],
            \PDO::PARAM_STR  
        );
        $statement->bindValue(
            ':last_synced',
            $params['last_synced'],
            \PDO::PARAM_STR
        );
        $statement->execute();
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
                source_last_record = :source_last_record
        ');
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
        $statement->execute();
    }

    public function disconnect()
    {
        $this->pdo = null;
    }
}