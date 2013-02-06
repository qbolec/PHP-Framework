<?php
/**
 * te testy mają na celu udokumentować wiedzę na temat tego jak dziwnie działa oryginalny PDO i dlaczego trzeba było dopisać własny wrapper
 * @see PDOStatementExTest
 */
class PDOStatementTest extends FrameworkTestCase
{
  protected function getPDO(){
    $pdo = parent::getPDO();
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }
  private function getSUT($sql){
    $pdo = $this->getPDO();
    return $pdo->prepare($sql);
  }
  public function testRowCountAtBadMomentReturns0(){
    $s = $this->getSUT("SELECT 1 FROM user WHERE id=:id");
    //we did not execute the statment yet!
    $this->assertSame(0,$s->rowCount());
  }
  public function testBindValueAlwaysReturnsTrue(){
    $s = $this->getSUT("SELECT 1 FROM user WHERE id=:id");
    //this one is ok:
    $this->assertSame(true,$s->bindValue(':id',1,PDO::PARAM_INT));
    //this one is not ok at all, as '1' is not integer.
    //moreover we already bound id
    $this->assertSame(true,$s->bindValue(':id','1',PDO::PARAM_INT));
    //this is also bad to allow both :id and id
    $this->assertSame(true,$s->bindValue('id',1,PDO::PARAM_INT));
    //1 is not a string
    $this->assertSame(true,$s->bindValue('id',1,PDO::PARAM_STR));
    //'a' is not an integer
    $this->assertSame(true,$s->bindValue('id','a',PDO::PARAM_INT));
    //null is surely not an integer:
    $this->assertSame(true,$s->bindValue('id',null,PDO::PARAM_INT));
    //1 is not a null
    $this->assertSame(true,$s->bindValue('id',1,PDO::PARAM_NULL));
    //13 is not a valid type of parameter
    $this->assertSame(true,$s->bindValue(':id',1,13));
    //'atlantis' is not even mentioned in the SQL query string:
    $this->assertSame(true,$s->bindValue(':atlantis',1,PDO::PARAM_INT));
  }
  /**
   * this one is correct, as there is no such table
   * @expectedException PDOException
   */
  public function testExecuteSingalsProblemsWithException(){
    $s = $this->getSUT('SELECT 1 FROM atlantis WHERE atlantis=13');
    $s->execute();
  }
  public function testFetchBeforeExecuteReturnsFalse(){
    $s = $this->getSUT('SELECT 1 FROM atlantis WHERE atlantis=13');
    //we did not execute the statement yet!
    $this->assertSame(false,$s->fetch());
  }
  /**
   * @expectedException PDOException
   */
  public function testFetchThrowsAfterUpdate(){
    $s = $this->getSUT('UPDATE user SET id=id WHERE 1=2');
    $this->assertSame(true,$s->execute());
    //we executed, but it wasn't select!
    $s->fetch();
  }
  /**
   * @expectedException PDOException
   */
  public function testFetchThrowsAfterDelete(){
    $s = $this->getSUT('DELETE FROM user WHERE 1=2');
    $this->assertSame(true,$s->execute());
    //we executed, but it wasn't select!
    $s->fetch();
  }
  
  public function testFetchReturnsFalseOnNoResults(){
    $s = $this->getSUT('SELECT 1 FROM user WHERE 1=2');
    $this->assertSame(true,$s->execute());
    //we executed,but there was no results
    $this->assertSame(false,$s->fetch());
  }
}
?>
