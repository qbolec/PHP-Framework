<?php
class PathLogWriterTest extends FrameworkTestCase
{
  private function getSUT(){
    return new PathLogWriter();
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
  public function testContainsClassNameClue(){
    $w = $this->getSUT();
    $y = $w->describe(debug_backtrace(0),'akuku');
    $this->assertThat($y,$this->stringContains('PathLogWriterTest::testContainsClassNameClue'));
  }
  public function testPerformance(){
    $w = $this->getSUT();
    $backtrace = debug_backtrace(0);
    for($x=100;$x--;){
       $w->describe($backtrace,'akuku');
    }
  }
}
?>
