<?php
class PDOEx implements IPDO
{
  private $pdo;
  public function __construct($dsn,$username,$password){
    $this->pdo = new PDO($dsn,$username,$password);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES,false);
  }
  //jest taki atrybut jak PDO::ATTR_STATEMENT_CLASS
  //ale nie działa w połączeniu z persistent connections (wg dokumentacji z 2011 roku)
  public function prepare($sql){
    return new PDOStatementEx($this->pdo->prepare($sql));
  }
  public function lastInsertId(){
    return $this->pdo->lastInsertId();
  }
  public function insert_ignore_command(){
    return 'INSERT IGNORE';
  }
  public function strong_equality_operator(){
    return '<=>';
  }
  public function quote($value){
    return $this->pdo->quote($value);
  }
  private $order_to_suffix = array(
    IRelationManager::ASC => 'ASC',
    IRelationManager::DESC => 'DESC',
    IRelationManager::ASC_NULL_LAST => 'ASC',
    IRelationManager::DESC_NULL_LAST => 'DESC',
    IRelationManager::ASC_NULL_FIRST => 'ASC',
    IRelationManager::DESC_NULL_FIRST => 'DESC',
  );
  private $order_to_prefix = array(
    IRelationManager::ASC => null,
    IRelationManager::DESC => null,
    IRelationManager::ASC_NULL_LAST => 'IS NULL ASC',
    IRelationManager::DESC_NULL_LAST => 'IS NULL ASC',
    IRelationManager::ASC_NULL_FIRST => 'IS NULL DESC',
    IRelationManager::DESC_NULL_FIRST => 'IS NULL DESC',
  );
  public function order_by_clause($column_name,$order){
    $prefix = Arrays::grab($this->order_to_prefix,$order);
    return ($prefix===null?'':"`$column_name` $prefix,") . "`$column_name` " . Arrays::grab($this->order_to_suffix,$order);
  }
}
?>
