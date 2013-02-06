<?php
class UnexpectedMemberExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $e = new UnexpectedMemberException('key');
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
  }
}
?>
