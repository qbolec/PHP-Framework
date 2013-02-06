<?php
class LogClassifierTest extends FrameworkTestCase
{
  public function a($x,ILogClassifier $log_classifier){
    if($x==0){
      return $this->b('foo',$log_classifier);
    }else{
      return $this->a($x-1,$log_classifier);
    }
  }
  public function b($info,ILogClassifier $log_classifier){
    return $log_classifier->classify(debug_backtrace(0),$info);
  }
  public function c($x,ILogClassifier $log_classifier){
    if($x){
      return $this->a(3,$log_classifier);
    }else{
      return $this->b('bar',$log_classifier);
    }
  }
  private function getSUT(){
    $this->setUpConfig();
    return new LogClassifier();
  }
  public function testInterface(){
    $log_classifier = $this->getSUT();
    $this->assertInstanceOf('ILogClassifier',$log_classifier);
    $this->assertInstanceOf('IGetInstance',$log_classifier);
  }
  public function setUpConfig(){
    $this->setConfig(array(
      'logging' => array(
        'rules' => array(
          '%LogClassifierTest::a@8/.*/LogClassifierTest::b@%' => array(
            'verbosity' => 1,
            'priority' => LOG_WARNING,
          ),
          '%LogClassifierTest::b@[^/]+>"foo"%' => array(
            'verbosity' => 2,
            'priority' => LOG_WARNING,
          ),
          '%LogClassifierTest::b@[^/]+>"bar"%' => array(
            'verbosity' => 2,
            'priority' => null,
          ),
          '%%' => array(
            'verbosity' => 1,
            'priority' => null,
          ),
        ),
      ),
    ));
  }
  public function test1(){
    $log_classifier = $this->getSUT();
    $classification = $this->c(true,$log_classifier);
    $this->assertSame(1,$classification['verbosity']);
    $this->assertSame(LOG_WARNING,$classification['priority']);
  }
  public function test2(){
    $log_classifier = $this->getSUT();
    $classification = $this->a(0,$log_classifier);
    $this->assertSame(2,$classification['verbosity']);
    $this->assertSame(LOG_WARNING,$classification['priority']);
  }
  public function test3(){
    $log_classifier = $this->getSUT();
    $classification = $this->c(false,$log_classifier);
    $this->assertSame(2,$classification['verbosity']);
    $this->assertSame(null,$classification['priority']);
  }
  public function test4(){
    $log_classifier = $this->getSUT();
    $classification = $this->b("atlantis",$log_classifier);
    $this->assertSame(1,$classification['verbosity']);
    $this->assertSame(null,$classification['priority']);
  }
}
?>
