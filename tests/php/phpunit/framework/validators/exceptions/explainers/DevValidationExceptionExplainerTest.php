<?php
class DevValidationExceptionExplainerTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = new DevValidationExceptionExplainer();
    $this->assertInstanceOf('IValidationExceptionExplainer',$a);
  }
  public function testPaths(){
    $inner_ex = $this->getMock('IValidationException');
    $inner_ex
      ->expects($this->never())
      ->method('to_tree');
    $inner_ex
      ->expects($this->once())
      ->method('getMessage')
      ->will($this->returnValue('akuku'));

    $e = $this->getMock('IValidationException'); 
    $e
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array(
        'fields'=>array(
          'brick_road' => array(
            'errors'=>array($inner_ex),
          ),
        ),
      )));
    $a = new DevValidationExceptionExplainer();
    $x = $a->explain($e);
    $this->assertContains('akuku',$x);
    $this->assertContains('/brick_road',$x);
  }
}
?>
