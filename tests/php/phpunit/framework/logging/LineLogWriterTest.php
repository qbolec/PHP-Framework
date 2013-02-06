<?php
class LineLogWriterTest extends FrameworkTestCase //InfoLogWriter
{
  protected function getSUT(){
    return LineLogWriter::get_instance();
  } 
  public function testInterface(){
    $w = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$w);
    $this->assertInstanceOf('ILogWriter',$w);
    $this->assertInternalType('string',$w->describe(debug_backtrace(0),'akuku'));
  }
  public function testPreservesInfo(){
    $w = $this->getSUT();
    $x=$w->describe(debug_backtrace(0),'akuku');
    $this->assertThat($x,$this->stringContains('akuku'));
  }
  public function testDoesNotProduceNewLines(){
    $w = $this->getSUT();
    $x=$w->describe(debug_backtrace(0),"a\nku\nku");
    $this->assertThat($x,$this->logicalNot($this->stringContains("\n")));
  }
  private function describe($bigObject){
    $w = $this->getSUT();
    return $w->describe(debug_backtrace(0),'akuku');
  }
  public function testContainsFileNameClue(){
    $x=$this->describe($this);
    $this->assertThat($x,$this->stringContains('LineLogWriterTest.php'));
    $w = $this->getSUT();
    $y = $w->describe(debug_backtrace(0),'akuku');
    $this->assertThat($y,$this->stringContains('LineLogWriterTest'));//z jakiegoś powodu nie ma nazwy pliku ani numeru linii
  }
}
?>
