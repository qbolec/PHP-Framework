<?php
function file_get_contents_stub($filename){
  return 'stub';
}
class RequestFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $this->assertInstanceOf('IRequestFactory',RequestFactory::get_instance());
  }
  public function testFromGlobals(){
    $_POST['post_a'] = 'a';
    $_GET['get_b'] = 'b';
    $_SERVER['SERVER_NAME'] = 'vanisoft.pl';
    
    $content_type ='application/x-www-form-urlencoded';
    $_SERVER['CONTENT_TYPE'] = $content_type ;
    $if_none_match = 'something';
    $_SERVER['HTTP_IF_NONE_MATCH'] = $if_none_match;

    $request=RequestFactory::get_instance()->from_globals();
    $this->assertInstanceOf('IRequest',$request);
    $this->assertEquals('vanisoft.pl',$request->get_host());
    $this->assertEquals('b',$request->get_uri_param('get_b'));
    $this->assertEquals('a',$request->get_post_value('post_a'));

    $this->assertEquals($content_type,$request->get_header('Content-Type'));
    $this->assertEquals($if_none_match,$request->get_header('If-None-Match'));
    $this->assertEquals('',$request->get_body());
  }
  public function testWithApacheHeaders(){
    if (!function_exists('apache_request_headers')) {
      eval('
        function apache_request_headers() {       
            return array(\'CONTENT-TYPE\' => \'application/x-www-form-urlencoded\', \'if-None-MATCH\' => \'something\');            
            }
      '); 
    }
    $request=RequestFactory::get_instance()->from_globals();
    $this->assertInstanceOf('IRequest',$request);
    $this->assertEquals('application/x-www-form-urlencoded',$request->get_header('Content-Type'));
    $this->assertEquals('something',$request->get_header('If-None-Match'));
    $this->assertEquals('',$request->get_body());
  }
  /**
   * @dataProvider postData
   */
  public function testFromMethodUrlPostData($method, $url, array $expected){
    $request=RequestFactory::get_instance()->from_method_url_post_data($method,$url,array());
    foreach($expected as $key => $value){
      switch($key){
         case 'SERVER_NAME': 
             $this->assertEquals($request->get_host(), $value);
             break;
         case 'SERVER_PORT':
             $this->assertEquals($request->get_port(), $value);
             break;
         case 'SCRIPT_URL':
             $this->assertEquals($request->get_path(), $value);
             break;
         case 'REQUEST_URI':
             $this->assertEquals($request->get_uri(), $value);
             break;
         case 'QUERY_STRING':
             $this->assertEquals($request->get_query(), $value);
             break;
         case 'REQUEST_METHOD':
             $this->assertEquals($request->get_method(), $value);
             break;
         case 'HTTPS':
             $this->assertEquals($request->is_https(), $value);
             break;
      }
    }
  } 
  public function postData(){
    return array(
      array(IRequest::METHOD_POST, 'http://wp.pl', array('SERVER_NAME'=>'wp.pl','SERVER_PORT'=>80,'SCRIPT_URL'=>'/','REQUEST_URI'=>'/')),
      array(IRequest::METHOD_POST, 'http://wp.pl:8088/test.php', array('SERVER_NAME'=>'wp.pl','SERVER_PORT'=>8088,'SCRIPT_URL'=>'/test.php', 'REQUEST_URI'=>'/test.php')),
      array(IRequest::METHOD_POST, 'http://wp.pl/test.php?time=5', array('SERVER_NAME'=>'wp.pl','SERVER_PORT'=>80,'SCRIPT_URL'=>'/test.php','QUERY_STRING'=>'time=5','REQUEST_URI'=>'/test.php?time=5')),
      array(IRequest::METHOD_POST, 'https://wp.pl:8989/test.php?time=5', array('SERVER_NAME'=>'wp.pl','SERVER_PORT'=>8989,'SCRIPT_URL'=>'/test.php','QUERY_STRING'=>'time=5','HTTPS'=>true)),
    );
  }
  /**
   * @expectedException LogicException
   */
  public function testMalformedUrlPostData(){ 
    $logger = $this->getMock('Logger', array('log'));
    $logger
      ->expects($this->any())
      ->method('log');
     
    $framework = $this->getMock('Framework',array('get_logger'));
    $framework
      ->expects($this->any())
      ->method('get_logger')
      ->will($this->returnValue($logger));
    $this->set_global_mock('Framework',$framework);

    $request=RequestFactory::get_instance()->from_method_url_post_data(IRequest::METHOD_POST,'http:///wp.pl:1234/ttt/t/t/t/t:3',array());
  }
  public function testMalformedUrlButNotURIPostData(){
    $logger = $this->getMock('Logger', array('log'));
    $logger
      ->expects($this->once())
      ->method('log');

    $framework = $this->getMock('Framework',array('get_logger'));
    $framework
      ->expects($this->once())
      ->method('get_logger')
      ->will($this->returnValue($logger));
    $this->set_global_mock('Framework',$framework);
    $request=RequestFactory::get_instance()->from_method_url_post_data(IRequest::METHOD_POST,'file:///username:password@www.wp.pl/razdwa/razdwa.php?g=3',array());
  }
  public function testStreamSelectFailed(){
    $value = 'stubbed_content';
    $_SERVER['SERVER_NAME'] = 'vanisoft.pl';
    $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
   
    $stdlib = $this->getMock('IStdLib');
    $stdlib
      ->expects($this->once())
      ->method('file_get_contents')
      ->will($this->returnValue($value));
    $this->set_global_mock('StdLib',$stdlib);

    $request=RequestFactory::get_instance()->from_globals();
    $this->assertInstanceOf('IRequest',$request);
    $this->assertSame('vanisoft.pl',$request->get_host());
 
    $this->assertSame($value,$request->get_body());
  }
}
?>
