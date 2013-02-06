<?php
/**
 * @see PDOStatementTest
 */
class PDOStatementExTest extends FrameworkTestCase
{
  protected function getPDO(){
    $pdo = parent::getPDO();
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES,false);
    return $pdo;
  }
  private function getSUT($sql){
    $pdo = $this->getPDO();
    return new PDOStatementEx($pdo->prepare($sql));
  }
  /**
   * @dataProvider goodBindings
   */
  public function testBindValueSuccess($param,$value,$type){
    $s = $this->getSUT("SELECT 1 FROM user WHERE id=:id");
    $s->bindValue($param,$value,$type);
  }
  public function goodBindings(){
    return array(
      array(':id',1,PDO::PARAM_INT),
      array(':id',true,PDO::PARAM_BOOL),
      array(':id',null,PDO::PARAM_NULL),
      array(':id','1',PDO::PARAM_STR),
    );
  }
  /**
   * @dataProvider badBindings 
   * @expectedException IValidationException
   */
  public function testBindValueSelectivity($param,$value,$type){
    $s = $this->getSUT("SELECT 1 FROM user WHERE id=:id");
    $s->bindValue($param,$value,$type);
  }
  public function badBindings(){
    return array(
      array(':id','1',PDO::PARAM_INT),
      array('id',1,PDO::PARAM_INT),
      array(':id',1,PDO::PARAM_STR),
      array('id','a',PDO::PARAM_INT),
      array(':id',null,PDO::PARAM_INT),
      array(':id',1,PDO::PARAM_BOOL),
      array(':atlantis',1,PDO::PARAM_INT),
      array(':id',1,13),
    );
  }
  /**
   * @expectedException Exception
   */
  public function testBindingTwiceThrows(){
    $s = $this->getSUT("SELECT 1 FROM user WHERE id=:id");
    $s->bindValue(':id',1,PDO::PARAM_INT);    
    $s->bindValue(':id',1,PDO::PARAM_INT);    
  }
  /**
   * this one is correct, as there is no such table
   * @expectedException PDOException
   */
  public function testExecuteSingalsProblemsWithException(){
    $s = $this->getSUT('SELECT 1 FROM atlantis WHERE atlantis=13');
    $s->execute();
  }
  /**
   * @expectedException LogicException
   */
  public function testFetchBeforeExecute(){
    $s = $this->getSUT('SELECT 1 FROM atlantis WHERE atlantis=13');
    //we did not execute the statement yet!
    $s->fetch();
  }
  /**
   * @expectedException LogicException
   */
  public function testFetchAllBeforeExecute(){
    $s = $this->getSUT('SELECT 1 FROM atlantis WHERE atlantis=13');
    //we did not execute the statement yet!
    $s->fetchAll();
  }


  /**
   * @expectedException PDOException
   */
  public function testFetchThrowsAfterUpdate(){
    $s = $this->getSUT('UPDATE user SET id=id WHERE 1=2');
    $s->execute();
    //we executed, but it wasn't select!
    $s->fetch();
  }
  /**
   * @expectedException PDOException
   */
  public function testFetchThrowsAfterDelete(){
    $s = $this->getSUT('DELETE FROM user WHERE 1=2');
    $s->execute();
    //we executed, but it wasn't select!
    $s->fetch();
  }
  
  public function testFetchReturnsFalseOnNoResults(){
    $s = $this->getSUT('SELECT 1 FROM user WHERE 1=2');
    $s->execute();
    //we executed,but there was no results
    $this->assertSame(false,$s->fetch());
  }
  /**
   * @expectedException LogicException
   */
  public function testRowCountAtBadMomentThrows(){
    $s = $this->getSUT('SELECT 1 FROM user WHERE 1=2');
    $s->rowCount();
  }
  /**
   * niestety nie da się przeskoczyć tego, że PDO zamienia wszystko na stringi
   */
  public function testFetchAll(){
    $s = $this->getSUT('SELECT 1');
    $s->execute();
    $result = $s->fetchAll();
    $expected = array(array('1'=>'1'));
    $this->assertTrue($expected===$result);
  }
}
?>
