<?php
namespace Ifp\VAgent\Adapter;

class MysqlSourceAdapter implement SourceAdapterInterface
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

    public function countTotalItems()
    {
        $statement = $this->pdo->prepare('
            SELECT COUNT(*) AS count
            FROM :table_name AS tn'
        );
        $statement->bindValue(':table_name', (string) $tableName, \PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC)
        var_dump($row);
    }
}