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
}
?>
