<?php
//jak testować abstrakcyjną klasę?
//może chociaż sprawdzę, czy resolve wywołuje resolve_path jak należy?
class AbstractRouterMock extends AbstractRouter
{
  public $last_path;
  const RESULT=42;
  public function resolve_path(array $path, IRequestEnv $env){
    $this->last_path = $path;
    return self::RESULT;  
  }
}
class AbstractRouterTest extends PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider paths
   */
  public function testResolvesPath(array $parts,$path){
    $mockRequest = $this->getMock('IRequest');
    $mockRequest
      ->expects($this->any())
      ->method('get_path')
      ->will($this->returnValue($path));
    
 
    $mockRouter = new AbstractRouterMock();
    $mockEnv = new RequestEnv($mockRequest);
    $result = $mockRouter->resolve($mockEnv);
    $this->assertEquals($parts,$mockRouter->last_path);
    $this->assertEquals($mockRouter::RESULT, $result);
  }
  public function paths(){
    return array(
      array(array('a','b'),'/a/b'),
      array(array('a','b'),'/a/b/'),
      array(array(),'/'),
      array(array('a'),'/a'),
      array(array('a'),'/a/'),
    );
  }
}
?>
