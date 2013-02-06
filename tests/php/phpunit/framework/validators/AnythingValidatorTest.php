<?php
class AnythingValidatorTest extends FrameworkTestCase
{
  public function testInterface(){
    $v = new AnythingValidator();
    $this->assertInstanceOf('IValidator',$v);
  }
  public function testStaysCool(){
    $v = new AnythingValidator();
    $this->assertSame(null,$v->get_error(null));
    $this->assertSame(null,$v->get_error("whatever"));
  }
}
?>
