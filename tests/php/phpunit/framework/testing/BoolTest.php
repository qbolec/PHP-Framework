<?php
class BoolTest extends FrameworkTestCase
{
  public function testBool(){
    $this->assertIsPermutationOf(array(array(false),array(true)), $this->getBool());
    $this->assertSame(array(array(false),array(true)), $this->getBool());
    $this->assertNotSame(array(array(0),array(true)), $this->getBool());
    $this->assertNotSame(array(array(false),array(1)), $this->getBool());
  }
}
?>
