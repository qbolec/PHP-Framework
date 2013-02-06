<?php
class SQLiteTest extends FrameworkTestCase
{
  public function testConstructor(){
    new PDO('sqlite::memory:');
  }
  public function testEx(){
    $pdo = new PDOEx('sqlite::memory:','whoever','whatever');
    $this->assertInstanceOf('IPDO',$pdo);
  }
  public function testPrepare(){
    $pdo = new PDOEx('sqlite::memory:','whoever','whatever');
    $q = $pdo->prepare('CREATE TABLE some_table ( id int )');
    $this->assertInstanceOf('IPDOStatement',$q);
  }
  public function testCreateTable(){
    $pdo = new PDOEx('sqlite::memory:','whoever','whatever');
    $q = $pdo->prepare('CREATE TABLE some_table ( id int )');
    $q->execute();
  }
  /**
   * @expectedException PDOException
   */
  public function testPrepareHasSideEffects(){
    $pdo = new PDOEx('sqlite::memory:','whoever','whatever');
    $q = $pdo->prepare('CREATE TABLE some_table ( id int )');
    $q->execute();
    $q2 = $pdo->prepare('CREATE TABLE some_table ( id int )');
  }
  public function testCreateInsertSelectDeleteDrop(){
    $pdo = new PDOEx('sqlite::memory:','whoever','whatever');
    $q = $pdo->prepare('CREATE TABLE some_table ( id int )');
    $q->execute();
    $q = $pdo->prepare('INSERT INTO some_table ( id ) VALUES (:id)');
    $q->bindValue(':id',1,PDO::PARAM_INT);
    $q->execute();
    $q->bindValue(':id',2,PDO::PARAM_INT);
    $q->execute();
    $q->bindValue(':id',5,PDO::PARAM_INT);
    $q->execute();
    $q = $pdo->prepare('SELECT id FROM some_table WHERE id > :min_id ORDER BY id');
    $q->bindValue(':min_id',1,PDO::PARAM_INT);
    $q->execute();
    $this->assertSame(array(array('id'=>'2'),array('id'=>'5')),$q->fetchAll());
    $q = $pdo->prepare('DELETE FROM some_table WHERE id = 2');
    $q->execute();
    $this->assertSame(1,$q->rowCount());
    $q = $pdo->prepare('DROP TABLE some_table');
    $q->execute();
  }
}
?>
