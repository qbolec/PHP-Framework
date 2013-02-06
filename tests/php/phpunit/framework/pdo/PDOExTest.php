<?php
class PDOExTest extends FrameworkTestCase
{
  public function testInterface(){
    $c=$this->get_test_pdo_config();
    $pdo = new PDOEx($c['dsn'],$c['username'],$c['password']);
    $this->assertInstanceOf('IPDO',$pdo);
    $this->assertInstanceOf('IPDOStatement',$pdo->prepare('SELECT 1'));
  }
}
?>
