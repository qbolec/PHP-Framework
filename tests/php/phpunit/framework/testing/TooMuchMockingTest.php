<?php
class TooMuchMockingTest extends FrameworkTestCase
{
  /**
    * @expectedException TooMuchMockingException
  */
  public function testTooMuchMocking(){
    $framework1 = $this->getMock('Framework');
    $framework2 = $this->getMock('Framework');
    $this->set_global_mock('Framework', $framework1);
    $this->set_global_mock('Framework', $framework2);
  }
}
?>
