<?php
class InfoLogWriterTest extends FrameworkTestCase
{
  protected function getSUT(){
    return InfoLogWriter::get_instance();
  }
  public function testInterface(){
    $w = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$w);
    $this->assertInstanceOf('ILogWriter',$w);
    $this->assertInternalType('string',$w->describe(debug_backtrace(),'akuku'));
  }
  public function testPreservesInfo(){
    $w = $this->getSUT();
    $x=$w->describe(debug_backtrace(),'akuku');
    $this->assertThat($x,$this->stringContains('akuku'));
  }
  public function testDoesNotProduceNewLines(){
    $w = $this->getSUT();
    $x=$w->describe(debug_backtrace(),"a\nku\nku");
    $this->assertThat($x,$this->logicalNot($this->stringContains("\n")));
  }
}
?>
