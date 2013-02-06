<?php
class AHandler extends Handler
{
  public function handle(IRequestEnv $env){
    throw new LogicException("should not get called");
  }
}
class RRouter extends Router
{
  protected $routing_table = array(
    '' => 'AHandler',
  );
}
class RouterTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException HTTPNotFoundException
   * @dataProvider paths
   */
  public function testEmpty($path)
  {
    $router = new Router();
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->any())
      ->method('get_path')
      ->will($this->returnValue($path));
    $env = new RequestEnv($request);
    $router->resolve($env);
  }
  public function paths(){
    return array(
      array('/'),
      array('/a'),
      array('/a/b'),
      array('/10'),
    );
  }
  /**
   * @dataProvider fullPaths
   */
  public function testFull($path){
    $router = $this->getMock('Router',array('get_routing_table'));
    $router
      ->expects($this->any())
      ->method('get_routing_table')
      ->will($this->returnValue(array(
        ''=>'AHandler',
        'a'=>'RRouter',
        '10'=>'AHandler',
      )));
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->any())
      ->method('get_path')
      ->will($this->returnValue($path));
    $env = new RequestEnv($request);
    $resolution = $router->resolve($env);
    $this->assertInstanceOf('IResolution',$resolution);
    $this->assertEquals($env,$resolution->get_env());
    $this->assertInstanceOf('AHandler',$resolution->get_handler());
  }
  public function fullPaths(){
    return array(
      array('/'),
      array('/a'),
      array('/10'),
    );
  }
}
?>
