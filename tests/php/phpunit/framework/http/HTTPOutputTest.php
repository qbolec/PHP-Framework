<?php
class HTTPOutputTest extends PHPUnit_Extensions_OutputTestCase
{
  public function testSingleton(){
    $a = HTTPOutput::get_instance();
    $b = HTTPOutput::get_instance();
    $this->assertSame($a,$b);
  }
  public function testOutputness(){
    $this->assertInstanceOf('IOutput',HTTPOutput::get_instance());
  }
  /**
   * @dataProvider bodys
   */
  public function testBody($body){
    $this->expectOutputString($body);
    HTTPOutput::get_instance()->send_body($body);
  }
  public function bodys(){
    return array(
      array(''),
      array('&amp;'),
      array('%20'),
    );
  }
  /**
   * @expectedException LogicException
   */
  public function testHeadersAlreadySent(){
    $this->expectOutputString('hej');
    $output = new HTTPOutput();
    $output->send_body('hej');
    $output->send_status(200,'OK');
  }
  /**
   * @expectedException LogicException
   */
  public function testHeadersAlreadySent2(){
    $this->expectOutputString('hej');
    $output = new HTTPOutput();
    $output->send_body('hej');
    $output->send_header('X-Whatever','whatever');
  }
  public function testBodyOK(){
    $this->expectOutputString('hej');
    $output = new HTTPOutput();
    $output->send_status(200, 'OK');
    $output->send_header('X-Whatever','whatever');
    $output->send_body('hej');   
  }   
}
?>
