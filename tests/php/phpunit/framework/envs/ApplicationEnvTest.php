<?php
class ApplicationEnvTest extends FrameworkTestCase
{
  public function getSUT(){
    $request = $this->getMock('IRequest');
    return new ApplicationEnv($request);
  }
  public function testInterface(){
    $env = $this->getSUT();
    $this->assertInstanceOf('IRequestEnv',$env);
    $this->assertInstanceOf('IApplicationEnv',$env);
  }
  public function testGrabDataEnv(){
    $env = $this->getSUT();
    $data_env = $this->getMock('IDataEnv');
    $env->set(ApplicationEnv::DATA,$data_env);
    $this->assertSame($data_env, $env->grab(ApplicationEnv::DATA));
  }
  /**
   * @expectedException LogicException
   */
  public function testSetDataEnvFailure(){
    $env = $this->getSUT();
    $bad_env = $this->getMock('IEnv');
    $env->set(ApplicationEnv::DATA,$bad_env);
  }
  
}
?>
