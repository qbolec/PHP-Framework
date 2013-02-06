<?php
class StackTraceLogWriterTest extends FrameworkTestCase
{
  protected function getSUT(){
    return StackTraceLogWriter::get_instance();
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
    $this->assertThat($x,$this->stringContains('StackTraceLogWriterTest.php'));
    $w = $this->getSUT();
    $y = $w->describe(debug_backtrace(0),'akuku');
    $this->assertThat($y,$this->stringContains('StackTraceLogWriterTest'));//z jakiegoś powodu nie ma nazwy pliku ani numeru linii
  }
  private function step_b(array $z){
    $w = $this->getSUT();
    return $w->describe(debug_backtrace(0),'akuku');
  }
  private function step_a($x){
    return $this->step_b(array(456,"789"));
  }
  public function testContainsFunctionsAndArgs(){
    $x = $this->step_a(123,$this);
    $this->assertThat($x,$this->stringContains('step_a'));
    $this->assertThat($x,$this->stringContains('step_b'));
    $this->assertThat($x,$this->stringContains('123'));
    $this->assertThat($x,$this->stringContains('456'));
    $this->assertThat($x,$this->stringContains('789'));
  }
}
?>
