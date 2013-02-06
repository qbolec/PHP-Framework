<?php
class WrongValueExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $e = new WrongValueException('key');
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
  }
}
?>
