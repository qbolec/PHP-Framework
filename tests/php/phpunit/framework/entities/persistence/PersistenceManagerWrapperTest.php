<?php
class PersistenceManagerWrapperTest extends FrameworkTestCase
{
  private function getWrapped(IPersistenceManager $pm){
    return new PersistenceManagerWrapper($pm);
  }
  public function testInterface(){
    $pm = $this->getMock('IPersistenceManager');
    $this->assertInstanceOf('IPersistenceManager',$this->getWrapped($pm));
  }
  public function testForwardsGetById(){
    $data = array('id'=>42,'person_id'=>'abc');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($data));
    $this->assertSame($data, $this->getWrapped($pm)->get_by_id(42)); 
  }
  /**
   * @dataProvider getBool
   */
  public function testForwardsDeleteById($success){
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('delete_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($success));
    $this->assertSame($success, $this->getWrapped($pm)->delete_by_id(42)); 
  }
  public function testForwardsInsert(){
    $data = array('id'=>42,'person_id'=>'abc');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data));
    $this->getWrapped($pm)->insert($data); 
  }
  public function testForwardsInsertAndAssignId(){
    $data = array('person_id'=>'abc');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('insert_and_assign_id')
      ->with($this->equalTo($data))
      ->will($this->returnValue(42));
    $this->assertSame(42, $this->getWrapped($pm)->insert_and_assign_id($data)); 
  }
  public function testForwardsSave(){
    $new_data = array('id'=>42,'person_id'=>'def');
    $old_data = array('id'=>42,'person_id'=>'abc');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo($new_data),$this->equalTo($old_data));
    $this->getWrapped($pm)->save($new_data,$old_data); 
  }
  public function testForwardsMultiGetByIds(){
    $data = array(42=>array('id'=>42,'person_id'=>'abc'));
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,44,43)))
      ->will($this->returnValue($data));
    $this->assertSame($data, $this->getWrapped($pm)->multi_get_by_ids(array(42,43,44))); 
  }
  public function testGetFieldsDescriptor(){
    $fd = $this->getMock('IFieldsDescriptor');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($fd));
    $this->assertSame($fd,$this->getWrapped($pm)->get_fields_descriptor());
  } 
}
?>
