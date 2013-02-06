<?php
class LogWriterFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $lf = LogWriterFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$lf);
    $this->assertInstanceOf('ILogWriterFactory',$lf);
    $this->assertInstanceOf('ILogWriter',$lf->get_by_verbosity(0));
  }
  public function testShouldNotFail(){
    $lf = LogWriterFactory::get_instance();
    $this->assertInstanceOf('ILogWriter',$lf->get_by_verbosity('bananas'));
  }
  public function testShouldHandleMax(){
    $lf = LogWriterFactory::get_instance();
    $this->assertInternalType('int',$lf->get_max_verbosity());
    for($l=0;$l<=$lf->get_max_verbosity();++$l){
      $this->assertInstanceOf('ILogWriter',$lf->get_by_verbosity($l));
    }
  }
}
?>
