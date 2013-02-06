<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{
  public function testEmpty(){
    $response = new Response(array(),'',200,'OK');
    $this->assertInstanceOf('IResponse',$response);

    $mock_output = $this->getMock('IOutput');
    $mock_output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo(''));
    $mock_output
      ->expects($this->never())
      ->method('send_header');
    $mock_output
      ->expects($this->once())
      ->method('send_status')
      ->with($this->equalTo(200),$this->equalTo('OK'));

    $response->send($mock_output);
  }
  /**
   * @dataProvider simple
   */
  public function testSimple($key,$value,$body,$code,$text){
    
    $response = new Response(array($key=>$value),$body,$code,$text);
    $this->assertInstanceOf('IResponse',$response);
    $mock_output = $this->getMock('IOutput');
    $mock_output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo($body));
    $mock_output
      ->expects($this->once())
      ->method('send_header')
      ->with($this->equalTo($key),$this->equalTo($value));
    $mock_output
      ->expects($this->once())
      ->method('send_status')
      ->with($this->equalTo($code),$this->equalTo($text));
    $response->send($mock_output);
  }
  public function simple(){
    return array(
      array('Key','value','body',404,'Not found'),
    );
  }
}
?>
