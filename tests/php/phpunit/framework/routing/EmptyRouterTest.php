<?php
class EmptyRouterTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException HTTPNotFoundException
   */
  public function testIsEmpty()
  {
    $r = new EmptyRouter();
    $mock_request = $this->getMock('IRequest');
    $mock_env = new RequestEnv($mock_request);
    $r->resolve_path(array(),$mock_env);
  }
}
?>
