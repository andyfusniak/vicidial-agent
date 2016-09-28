<?php
namespace Ifp\VAgent\Adapter\Source;

use Ifp\VAgent\Resource\Item;

class MysqlSourceAdapter extends SourceAdapterAbstract implements SourceAdapterInterface
{
    /**
     * @var \PDO MySQL PDO connection
     */
    protected $pdo;

    /**
     * @var array
     */
    protected $config;


    /**
     * Function constructor
     * @param \PDO MySQL PDO Object dependency
     */
    public function __construct(\PDO $pdo, array $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Get the primary key field name
     *
     * @return string primary key field name
     */
    private function getPrimaryKeyFieldName()
    {
        return $this->config['primary_key_field_name'];
    }

    /**
     * Get the table name
     *
     * @return string table name
     */
    private function getTableName()
    {
        return $this->config['table_name'];
    }

    private function getSelectFieldsSqlString()
    {
        $list = array_keys($this->config['select_field_mappings']);
        
        // always include the primary key as the first element
        // in the select list
        array_unshift($list, $this->getPrimaryKeyFieldName());

        return implode(', ',
            array_map(function($v) {
                return '`' . $v . '`';
                },
                $list
            )
        );
    }

    /**
     * Initialise the adapter
     */
    public function init()
    {
        $this->cursor = 0;
    }

    /**
     * @return int total number of items in the data source
     */
    public function countTotalItems()
    {
        // warning the table names aren't sanitised
        $statement = $this->pdo->prepare('
            SELECT COUNT(:primary_key_field_name) AS cnt
            FROM ' . $this->getTableName()
        );
        $statement->bindValue(
            ':primary_key_field_name',
            $this->getPrimaryKeyFieldName(),
            \PDO::PARAM_STR
        );
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['cnt'];
    }

    /**
     * Get the last row insert from the datasource
     *
     * @return int the last record insert
     */
    public function getLastRecordId()
    {
        $statement = $this->pdo->prepare('
            SELECT AUTO_INCREMENT AS next_record_id
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = "' . $this->getTableName() . '"'
        );
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['next_record_id'] - 1;
    }
    
    /**
     * @return int current cursor position in the list
     */
    public function getCursorPosition()
    {
        return $this->cursor;
    }

    /**
     * @return Items
     */
    public function getAllItems()
    {

    }

    /**
     * Map the source fields to the destination fields preserving the values
     *
     * @return array
     */
    private function sourceDestMapping($row)
    {
        $dest = [];
        $primaryKeyFieldName = $this->getPrimaryKeyFieldName();
        $selectFieldMappings = $this->config['select_field_mappings'];
        $staticFields = $this->config['static_fields'];

        // build a new associative array with the destination name/value pairs
        foreach ($row as $name => $value) {
            if (array_key_exists($name, $selectFieldMappings)) {
                $key = $selectFieldMappings[$name];
                $dest[$key] = $row[$name];
            } else if ($name === $primaryKeyFieldName) {
                $dest['id'] = $row[$name];
            }
        }

        // static fields over ride any prior mappings
        foreach ($staticFields as $destName => $value) {
            $dest[$destName] = $value;
        }

        return $dest;
    }

    /**
     * Build an Item instance from the destination hash map
     *
     * @param array $dest the hash map to use for building
     * @return Item a new Item instance
     */
    private function buildItemFromDestMap(array $dest)
    {
        $item = new Item();
        // we unset dest mappings as we go, so we can debug
        // to see any missing ones left behind
        if (isset($dest['id'])) {
            $item->setId($dest['id']);
            unset($dest['id']);
        }

        if (isset($dest['phone_number'])) {
            $item->setPhoneNumber($dest['phone_number']);
            unset($dest['phone_number']);
        }

        if (isset($dest['first_name'])) {
            $item->setFirstName($dest['first_name']);
            unset($dest['first_name']);
        }

        if (isset($dest['last_name'])) {
            $item->setLastName($dest['last_name']);
            unset($dest['last_name']);
        }

        // the rest of the $dest are custom
        $item->setCustomParams($dest);
        
        return $item;
    }

    /**
     * Items are pulled in FIFO order i.e. descending id
     * to ensure the most recent leads are acted on first
     *
     * @return Item
     */
    public function getNextItem()
    {
        $statement = $this->pdo->prepare(
            'SELECT ' . $this->getSelectFieldsSqlString()
            . ' FROM ' . $this->getTableName()
            . ' ORDER BY ' . $this->getPrimaryKeyFieldName() . ' DESC'
            . ' LIMIT ' . $this->getCursorPosition() . ',1'
        );
        $statement->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        $dest = $this->sourceDestMapping($row);
        $this->cursor++;
        return $this->buildItemFromDestMap($dest);
    }

    /**
     * @return Items
     */
    public function getNextPage()
    {
    }

    /**
     * @param int $batchSize maximum number of items to pull per page
     */
    public function setPageSize($size)
    {
    }
}