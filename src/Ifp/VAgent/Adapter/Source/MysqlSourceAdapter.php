<?php
namespace Ifp\VAgent\Adapter\Source;

use Monolog\Logger;
use Ifp\VAgent\Resource\Item;
use Ifp\VAgent\Resource\ItemCollection;

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
     * @var Logger
     */
    protected $log;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $filter;

    /**
     * @var bool
     */
    protected $hasIsDupColumn = null;

    /**
     * Function constructor
     *
     * @param \PDO $pdo MySQL PDO Object dependency
     * @param array $config the source config for this source
     * @param array optional parameters to configure this data source
     */
    public function __construct(\PDO $pdo, array $config, $options = null)
    {
        $this->pdo = $pdo;
        $this->config = $config;

        if ($options) {
            if (isset($options['default_page_size'])) {
                $this->pageSize = (int) $options['default_page_size'];
            }
        }

        $this->name = $config['name'];
        $this->filter = isset($config['filter']) ? $config['filter'] : null;
        parent::__construct();
    }

    /**
     * Get the name of this data source
     *
     * @return string name of this data source
     */
    public function getName()
    {
        return $this->name;
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
            . ' ORDER BY ' . $this->getPrimaryKeyFieldName() . ' ASC'
            . ' LIMIT ' . $this->getCursorPosition() . ',1'
        );
        $statement->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        $dest = $this->sourceDestMapping($row);
        $this->cursor++;
        return $this->buildItemFromDestMap($dest);
    }

    /**
     * Checks the information_schema to see if the is_dups
     * column is in use
     *
     * @return bool true if the is_dups column is in use
     */
    public function hasIsDupColumn()
    {
        // cache the value so it happens only once
        if (null !== $this->hasIsDupColumn) {
            return $this->hasIsDupColumn;
        }

        $statement = $this->pdo->prepare('
            SELECT COUNT(*) AS cnt
            FROM information_schema.COLUMNS
            WHERE
                TABLE_SCHEMA = DATABASE() AND
                TABLE_NAME = :table_name AND
                COLUMN_NAME = "is_dup"
        ');
        $statement->bindValue(
            ':table_name',
            $this->getTableName(),
            \PDO::PARAM_STR
        );
        $statement->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        $cnt = (int) $row['cnt'];

        if ($cnt > 0) {
            return $this->hasIsDupColumn = true;
        }

        return $this->hasIsDupColumn = false;
    }

    /**
     * Get the next page of Item objects as a ItemCollection
     *
     * @return ItemCollection
     */
    public function getNextPage()
    {
        $page = ($this->page * $this->pageSize);

        $where = [];
        if (true === $this->hasIsDupColumn()) {
            $where[] = 'is_dup = 0';
        }

        if (null !== $this->filter) {
            $where[] = $this->filter;
        }

        $first = true;
        $whereClause = '';
        foreach ($where as $item) {
            $whereClause .= ((true === $first) ? ' WHERE ' : ' AND ')
                         . $item;
        }

        $sql = 'SELECT ' . $this->getSelectFieldsSqlString()
             . ' FROM ' . $this->getTableName();
        $sql .= $whereClause;
        $sql .= ' ORDER BY ' . $this->getPrimaryKeyFieldName() . ' ASC'
             . ' LIMIT ' . $page . ',' . $this->pageSize;

        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (0 === $statement->rowCount()) {
            if ($this->log) {
                $this->log->debug('getNextPage() returning null as no more items to fetch');
            }
            return null;
        }

        if ($this->log) {
            $this->log->debug('Fetched page the next ' . $this->pageSize . ' from page ' . $page);
        }

        $itemCollection = new ItemCollection();
        foreach ($rows as $row) {
            $dest = $this->sourceDestMapping($row);
            $itemCollection->add($this->buildItemFromDestMap($dest));
        }
        $this->page++;
        return $itemCollection;
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
