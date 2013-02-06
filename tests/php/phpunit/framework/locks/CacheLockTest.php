<?php
class CacheLockTest extends FrameworkTestCase
{
  public function getSUT(){
    return Framework::get_instance()
             ->get_lock_factory()
             ->get_lock("test"); 
  }
  public function testInterface(){
    $cache_lock = $this->getSUT();
    $this->assertInstanceOf('CacheLock', $cache_lock);
  }
  /**
   * @expectedException DeadlockException
   */
  public function testForceDeadlock(){
    runkit_function_redefine('usleep', '', 'return;');
    $lock = $this->getSUT();
    $lock2 = $this->getSUT(); 
  }
}
?>
