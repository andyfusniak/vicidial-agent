<?php
namespace Ifp\VAgent\Adapter\Source;

class MysqlSourceAdapter extends SourceAdapterAbstract implements SourceAdapterInterface
{
    /**
     * @var \PDO MySQL PDO connection
     */
    protected $pdo;


    /**
     * Function constructor
     * @param \PDO MySQL PDO Object dependency
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Initialise the adapter
     */
    public function init();

    /**
     * @return int total number of items in the data source
     */
    public function countTotalItems()
    {
        $statement = $this->pdo->prepare('
            SELECT COUNT(*) AS count
            FROM :table_name AS tn'
        );
        $statement->bindValue(':table_name', (string) $tableName, \PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        var_dump($row);
    }
    
    /**
     * @return int current cursor position in the list
     */
    public function getCursorPosition();

    /**
     * @return Items
     */
    public function getAllItems();

    /**
     * @return Item
     */
    public function getNextItem();

    /**
     * @return Items
     */
    public function getNextPage();

    /**
     * @param int $batchSize maximum number of items to pull per page
     */
    public function setPageSize($size);
}