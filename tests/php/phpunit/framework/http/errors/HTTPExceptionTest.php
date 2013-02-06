<?php
//jak można przetestować wyjątek?
//można chociaż sprawdzić, że ma odpowiedni konstruktor
//i że jest wyjątkiem:)

class HTTPExceptionTest extends PHPUnit_Framework_TestCase
{
  public function testGenericError(){
    $r = $this->getMock('IRequestEnv');
    $e = new HTTPException('Bad request',400,$r);
    $this->assertInstanceOf('Exception',$e);
  }
  /**
   * @expectedException LogicException
   */
  public function testNotAnError(){
    $r = $this->getMock('IRequestEnv');
    $e = new HTTPException('OK',200,$r);
  }
  /**
   * @expectedException CouldNotConvertException
   */
  public function testNotANumber(){
    $r = $this->getMock('IRequestEnv');
    $e = new HTTPException(400,'Bad request',$r);
  }
}
?>
