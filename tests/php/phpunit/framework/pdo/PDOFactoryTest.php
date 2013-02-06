<?php
class PDOFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $dbs = $this->getMock('PDOFactory');
    $this->assertInstanceOf('IPDOFactory',$dbs);
  }
  private function get_different_config(){
    return array(
      'dsn' => 'dsn-1',
      'username' => 'username-1',
      'password' => 'password-1',
    );
  }
  private function config(){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(
        array(
          'pdos' => array(
            'masters' => array(
              'users' => array(
                0 => $this->get_test_pdo_config(),
                1 => $this->get_different_config(),
              ),
            ),
          ),
        )
      ));
    return $config; 
  }
  public function testLoadingSuccess(){
    $original_config = Config::set_instance($this->config());
    $db1 = $this->getMock('IPDO');
    $dbs = $this->getMock('PDOFactory',array('spawn'));
    $dbs
      ->expects($this->once())
      ->method('spawn')
      ->with($this->get_different_config())
      ->will($this->returnValue($db1));
    $db2=$dbs->get_pdo('users',1);
    $db3=$dbs->get_pdo('users',1);
    $this->assertSame($db1,$db2);
    $this->assertSame($db1,$db3);
    Config::set_instance($original_config);
  }
  public function testRealConnection(){
    $original_config = Config::set_instance($this->config());
    $dbs = $this->getMock('PDOFactory',array('nothing'));
    $pdo = $dbs->get_pdo('users',0);
    $this->assertInstanceOf('IPDO',$pdo);
    Config::set_instance($original_config);
  }  
}
?>
