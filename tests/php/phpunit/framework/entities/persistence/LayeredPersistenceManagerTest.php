<?php
class LayeredPersistenceManagerTest extends FrameworkTestCase
{
  public function testInterface(){
    $near = $this->getMock('IPersistenceManager');
    $far = $this->getMock('IPersistenceManager');
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
  /**
   * @expectedException NoSuchEntityException
   */
  public function testGetByIdMiss(){
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->throwException(new NoSuchEntityException(42)));
    $near
      ->expects($this->never())
      ->method('insert');
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->throwException(new NoSuchEntityException(42)));
    $pm = new LayeredPersistenceManager($near,$far);
    $pm->get_by_id(42);
  }
  public function testGetByIdTiesNearThenFar(){
    $data = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->throwException(new NoSuchEntityException(42)));
    $near
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data))
      ->will($this->returnValue(false));//powiedzmy, ze ktos inny w tym samym czasie dodal do cacheu
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($data));
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertSame($data,$pm->get_by_id(42));
  }
  public function testGetByIdDoesNotBotherFar(){
    $data = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($data));
    $near
      ->expects($this->never())
      ->method('insert');
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->never())
      ->method('get_by_id');
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertSame($data,$pm->get_by_id(42));
  }
  /**
   * @dataProvider getBool
   */
  public function testDeletesBoth($success){
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('delete_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue(false));
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('delete_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($success));
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertSame($success,$pm->delete_by_id(42));
  }
  public function testInsertAndAssignId(){
    $data_without_id = array(
      'person_id' => 'abc',
    );
    $id = 42;
    $data = array_merge($data_without_id, array('id'=>$id));
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data))
      ->will($this->returnValue(false));//ot ciekawy race condition: ktoś zdążył skeszować coś co dopiero dodałem
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('insert_and_assign_id')
      ->with($this->equalTo($data_without_id))
      ->will($this->returnValue($id));
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertSame($id,$pm->insert_and_assign_id($data_without_id));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsertSuccess($success){
    $data = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data))
      ->will($this->returnValue($success));
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data))
      ->will($this->returnValue(true));
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertSame(true,$pm->insert($data));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsertFailure($success){
    $data = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->never())
      ->method('insert');
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data))
      ->will($this->returnValue(false));
    $pm = new LayeredPersistenceManager($near,$far);
    $this->assertSame(false,$pm->insert($data));
  }
  public function testSave(){
    $new_data = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $old_data = array(
      'id' => 42,
      'person_id' => 'def',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo($new_data),$this->equalTo($old_data));
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo($new_data),$this->equalTo($old_data));
    $pm = new LayeredPersistenceManager($near,$far);
    $pm->save($new_data,$old_data);
  }
  public function testMultiGetByIds(){
    $user[42] = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $user[43] = array(
      'id' => 42,
      'person_id' => 'def',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,43,44)))
      ->will($this->returnValue(array(42=>$user[42])));
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(43,44)))
      ->will($this->returnValue(array(43=>$user[43])));
    $pm = new LayeredPersistenceManager($near,$far);
    $result=$pm->multi_get_by_ids(array(42,43,44));
    ksort($result);
    $this->assertSame($user,$result);
  }
  public function testMultiGetByIdsDoesNotBotherFar(){
    $user[42] = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $user[43] = array(
      'id' => 42,
      'person_id' => 'def',
    );
    $near = $this->getMock('IPersistenceManager');
    $near
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,43)))
      ->will($this->returnValue(array(42=>$user[42],43=>$user[43])));
    $far = $this->getMock('IPersistenceManager');
    $far
      ->expects($this->never())
      ->method('multi_get_by_ids');
    $pm = new LayeredPersistenceManager($near,$far);
    $result=$pm->multi_get_by_ids(array(42,43));
    ksort($result);
    $this->assertSame($user,$result);
  }
  public function testGetFieldsDescriptor(){
    $fd = $this->getMock('IFieldsDescriptor');
    $pms = array();
    for($i=0;$i<2;++$i){
      $pms[$i] = $this->getMock('IPersistenceManager');
      $pms[$i]
        ->expects($this->any())
        ->method('get_fields_descriptor')
        ->will($this->returnValue($fd));
    }
    $pm = new LayeredPersistenceManager($pms[0],$pms[1]);
    $this->assertSame($fd,$pm->get_fields_descriptor());
  }
}
?>
