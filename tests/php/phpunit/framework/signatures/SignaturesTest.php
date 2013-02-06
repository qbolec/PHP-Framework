<?php
class SignaturesTest extends FrameworkTestCase
{
  private function getSUT(){
    return new Signatures();
  }
  public function testInterface(){
    $s = $this->getSUT();
    $this->assertInstanceOf('ISignatures',$s);
    $this->assertInstanceOf('IGetInstance',$s);
  }
  public function testSign(){
    $s = $this->getSUT();
    $msg = 'whatever';
    $sig = $s->sign($msg);
    $this->assertInternalType('string',$sig);
    $this->assertRegExp('/^[[:xdigit:]]+$/',$sig);
    $this->assertSame($sig,$s->sign($msg));
  }
}
?>
