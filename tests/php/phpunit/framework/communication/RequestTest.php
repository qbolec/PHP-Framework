<?php

class RequestTest extends PHPUnit_Framework_TestCase
{
  public function testEmpty(){
    return new Request(array(),array(),array(),array(),'');
  }
  /**
   * @depends testEmpty
   */
  public function testDefaults(Request $request){
    $this->assertInstanceOf('IRequest',$request);
    $this->assertEquals($request->get_post_value('a','x'),'x');
    $this->assertEquals($request->get_post_value('a'),null);
    $this->assertEquals($request->get_uri_param('a','x'),'x');
    $this->assertEquals($request->get_uri_param('a'),null);
    $this->assertEquals($request->get_host(),null);
    $this->assertEquals($request->get_port(),null);
    $this->assertEquals($request->get_path(),'/');
    $this->assertEquals($request->get_query(),'');
    $this->assertEquals($request->get_scheme(),'http');
    $this->assertEquals($request->get_method(),null);
    $this->assertEquals(false,$request->is_post());
    $this->assertEquals(false,$request->is_https());
    $this->assertNull($request->get_uri());
    $this->assertEquals('',$request->get_body());
    $this->assertEquals('x',$request->get_header('Authentication','x'));
    $this->assertNull($request->get_header('Authentication'));
  }
  public function testRegular(){
    return new Request(array('a'=>'A '),array('b'=>'B '),array(
      'HTTPS'=>'on',
      'SERVER_PORT'=>443,
      'SERVER_NAME'=>'vanisoft.pl',
      'QUERY_STRING'=>'b=B%20',
      'SCRIPT_URL'=>'/oj/ej',
      'REQUEST_URI'=>'/oj/ej?b=B%20',
      'REQUEST_METHOD'=>'POST',
    ),array(
      'Content-Type'=>'application/x-www-form-urlencoded',
    ),'a=A%20');
  }
  /**
   * @depends testRegular
   */
  public function testHttpsPost(Request $request){
    $this->assertEquals('A ',$request->get_post_value('a','x'));
    $this->assertEquals('A ',$request->get_post_value('a'));
    $this->assertEquals('B ',$request->get_uri_param('b','x'));
    $this->assertEquals('B ',$request->get_uri_param('b'));
    $this->assertEquals('vanisoft.pl',$request->get_host());
    $this->assertEquals(443,$request->get_port());
    $this->assertEquals('/oj/ej',$request->get_path());
    $this->assertEquals('b=B%20',$request->get_query());//? czy decode?
    $this->assertEquals('https',$request->get_scheme());
    $this->assertEquals('POST',$request->get_method());
    $this->assertEquals(true,$request->is_post());
    $this->assertEquals(true,$request->is_https());
    $this->assertEquals('a=A%20',$request->get_body());
    $this->assertEquals('application/x-www-form-urlencoded',$request->get_header('Content-Type'));
    $this->assertEquals('/oj/ej?b=B%20',$request->get_uri());
  }
}
?>
