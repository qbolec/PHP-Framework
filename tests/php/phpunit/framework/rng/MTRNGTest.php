<?php
class MTRNGTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $rng = new MTRNG();
    $this->assertInstanceOf('IRNG',$rng);
    $this->assertInternalType('int',$rng->next());
  }
}
?>
