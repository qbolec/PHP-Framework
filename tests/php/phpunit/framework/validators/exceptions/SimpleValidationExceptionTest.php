<?php
class SimpleValidationExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $e = new SimpleValidationException(); 
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
  }
  public function testToTree(){
    $e = new SimpleValidationException(); 
    $this->assertSame(array('errors'=>array($e)),$e->to_tree());
  }
}
?>
