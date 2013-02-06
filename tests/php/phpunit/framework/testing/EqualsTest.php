<?php
class EqualsTest extends FrameworkTestCase
{
  public function testOneVsTrue(){
    $this->assertTrue(true==1);
    //but...
    $this->assertNotEquals(true,1);
  }
}
?>
