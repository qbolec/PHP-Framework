<?php
abstract class AbstractSimplePersistentEntitiesImp extends AbstractSimplePersistentEntities
{
  public function insert(array $data){
    return parent::insert($data);
  }
  public function insert_and_assign_id(array $data){
    return parent::insert_and_assign_id($data);
  }
}
class AbstractSimplePersistentEntitiesTest extends FrameworkTestCase
{
  private function getSUT(IPersistenceManager $persistence_manager){
    return $this->getMockForAbstractClass('AbstractSimplePersistentEntitiesImp',array($persistence_manager));
  }
  public function testInterface(){
    $persistence_manager = $this->getMock('IPersistenceManager');
    $a = $this->getSUT($persistence_manager);
    $this->assertInstanceOf('IEntities',$a);
  }
  public function testGetById(){
    $data=array('id'=>42,'a'=>'b');
    $persistence_manager = $this->getMock('IPersistenceManager');
    $persistence_manager
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($data));
    $entity = $this->getMock('IEntity');
    $a = $this->getSUT($persistence_manager);
    $a
      ->expects($this->once())
      ->method('from_data')
      ->with($this->equalTo($data))
      ->will($this->returnValue($entity));
    $this->assertSame($entity,$a->get_by_id(42));
  }
  public function testInsertAndAsignId(){
    $data=array('a'=>'b');
    $data_with_id=array('id'=>42,'a'=>'b');
    $persistence_manager = $this->getMock('IPersistenceManager');
    $persistence_manager
      ->expects($this->once())
      ->method('insert_and_assign_id')
      ->with($this->equalTo($data))
      ->will($this->returnValue(42));
    $entity = $this->getMock('IEntity');
    $a = $this->getSUT($persistence_manager);
    $a
      ->expects($this->once())
      ->method('from_data')
      ->with($this->equalTo($data_with_id))
      ->will($this->returnValue($entity));
    $this->assertSame($entity,$a->insert_and_assign_id($data));
  }
  public function testInsert(){
    $data_with_id=array('id'=>42,'a'=>'b');
    $persistence_manager = $this->getMock('IPersistenceManager');
    $persistence_manager
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data_with_id));
    $entity = $this->getMock('IEntity');
    $a = $this->getSUT($persistence_manager);
    $a
      ->expects($this->once())
      ->method('from_data')
      ->with($this->equalTo($data_with_id))
      ->will($this->returnValue($entity));
    $this->assertSame($entity,$a->insert($data_with_id));
  }
  public function testMultiGetByIds(){
    $data = array(42 => array('id'=>42,'a'=>'b'), 99 => array('id'=>99,'c'=>'d'));

    $persistence_manager = $this->getMock('IPersistenceManager');
    $persistence_manager
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->equalTo(array(42,99)))
      ->will($this->returnValue($data));

    $alias=$this;
    $a = $this->getSUT($persistence_manager);
    $a
      ->expects($this->exactly(2))
      ->method('from_data')
      ->will($this->returnCallback(
                     function($args) use ($alias){ 
                       $entity = $alias->getMock('IEntity');     
                       $entity
                          ->expects($alias->any())
                          ->method('get_id')
                          ->will($alias->returnValue($args['id']));
                       return $entity;
               }));
    $entities = $a->multi_get_by_ids(array(42,99));    
    $this->assertSame(42,$entities[42]->get_id());
    $this->assertSame(99,$entities[99]->get_id());
  }
}
?>
