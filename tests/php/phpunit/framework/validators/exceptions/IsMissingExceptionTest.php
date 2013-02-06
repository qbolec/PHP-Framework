<?php
class IsMissingExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $e = new IsMissingException('key');
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
  }
}
?>
