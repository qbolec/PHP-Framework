<?php
class AbstractExtendablePersistentEntitiesTest extends FrameworkTestCase
{
  public function testInterface(){
    $pm = $this->getMock('IPersistenceManager');
    $a= $this->getMockForAbstractClass('AbstractExtendablePersistentEntities',array($pm));
    $this->assertInstanceOf('IEntities',$a);
    $this->assertInstanceOf('IExtendableEntities',$a);
    $this->assertInstanceOf('IPersistenceManager',$a->get_persistence_manager());
    $this->assertInstanceOf('ISharedPersistence',$a);
  }
  public function testGetPersistenceManager(){
    $pm = $this->getMock('IPersistenceManager');
    $a= $this->getMockForAbstractClass('AbstractExtendablePersistentEntities',array($pm));
    $this->assertSame($pm,$a->get_persistence_manager());
  }
}
?>
