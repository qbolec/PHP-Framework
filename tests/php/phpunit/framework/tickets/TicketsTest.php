<?php
class TicketsTest extends FrameworkTestCase
{
  public function getSUT(){
    return new Tickets();
  }
  public function testInterface(){
    $t = $this->getSUT();
    $this->assertInstanceOf('ITickets',$t);
    $this->assertInstanceOf('IGetInstance',$t);
  }
  public function testGenerateConsistency(){
    $framework_mockery = array(
      'get_time' => 1234567890,
    );
    $framework = $this->getMock('Framework',array_keys($framework_mockery));
    $this->setMockery($framework,$framework_mockery);
    $this->set_global_mock('Framework',$framework);

    $id = 'something';
    $ttl = 10;

    $t = $this->getSUT();
    $ticket = $t->generate($id,$ttl);
    $this->assertSame($id,$t->get_id($ticket));
    $this->assertSame($ticket,$t->generate($id,$ttl));
    $this->assertNotSame($ticket,$t->generate($id,$ttl+1));
    $this->assertNotSame($ticket,$t->generate($id.'a',$ttl));

  }
  /**
   * @expectedException InvalidTicketException
   */
  public function testExpiration(){
    $the_time = 1234567890;
    $framework = $this->getMock('Framework',array('get_time'));
    $framework
      ->expects($this->atLeastOnce())
      ->method('get_time')
      ->will($this->returnCallback(function()use(&$the_time){return $the_time;}));
    $this->set_global_mock('Framework',$framework);

    $id = 'something';
    $ttl = 10;

    $t = $this->getSUT();
    $ticket = $t->generate($id,$ttl);
    $this->assertSame($id,$t->get_id($ticket));
    
    $the_time += $ttl;
    $t->get_id($ticket);
  }
}
