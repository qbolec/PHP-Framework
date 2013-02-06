<?php
class AbstractEditablePersistentEntitiesTest extends FrameworkTestCase
{
  private function getSUT($persistence_manager){
    return $this->getMockForAbstractClass('AbstractEditablePersistentEntities',array($persistence_manager));
  }
  public function testInterface(){
    $persistence_manager = $this->getMock('IPersistenceManager');
    $this->assertInstanceOf('IEditableEntities',$this->getSUT($persistence_manager));
  }
  public function testSave(){
    $current_data = array(
      'id' => 42,
      'a' => 'b',
    );
    $original_data = array(
      'a' => 'c',
    );
    $persistence_manager = $this->getMock('IPersistenceManager');
    $persistence_manager
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo($current_data),$this->equalTo($original_data))
      ->will($this->returnValue(true));
    $a=$this->getSUT($persistence_manager);
    $this->assertSame(true,$a->save($current_data,$original_data));
  }
}
?>
