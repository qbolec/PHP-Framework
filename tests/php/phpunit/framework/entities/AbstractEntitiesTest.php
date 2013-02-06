<?php
class AbstractEntitiesTest extends FrameworkTestCase
{
  public function testInterface(){
    $es = $this->getMockForAbstractClass('AbstractEntities');
    $this->assertInstanceOf('IEntities',$es);
    $this->assertInstanceOf('IGetInstance',$es);
  }
  /**
   * expectException NoSuchEntityException 
   */
  public function testMissMultiGetByIds(){
    $es = $this->getMockForAbstractClass('AbstractEntities');
    $es->multi_get_by_ids(array(12345));    
  }
  public function testMultiGetByIds(){
    $alias = $this;
    $es = $this->getMockForAbstractClass('AbstractEntities');
    $es
      ->expects($this->exactly(2))
      ->method('get_by_id')
      ->will($this->returnCallback(
                     function($id) use ($alias){
                       $entity = $alias->getMock('IEntity');
                       $entity
                          ->expects($alias->any())
                          ->method('get_id')
                          ->will($alias->returnValue($id));
                       return $entity;
               }));
 
    $entities = $es->multi_get_by_ids(array(12345, 42));
    $this->assertSame(12345, $entities[12345]->get_id());
    $this->assertSame(42, $entities[42]->get_id());
    $this->assertSame(2, count($entities));
  }
}
?>
