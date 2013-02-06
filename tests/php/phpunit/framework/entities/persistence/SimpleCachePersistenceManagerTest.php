<?php
class SimpleCachePersistenceManagerTest extends FrameworkTestCase
{
  private function getFieldsDescriptor(){
    return $this->getUserlikeFieldsDescriptor();
  }
  public function testInterface(){
    $fd = $this->getMock('IFieldsDescriptor');
    $cache = $this->untouchableCache();
    $pm = new SimpleCachePersistenceManager($fd,$cache,'prefix');
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
  public function testMultiGetByIds(){
    $cache =  new PrefetchingCacheWrapper(new ArrayCache());

    $user[42]=array(
      'id'=>42,
      'person_id'=>'abc',
    );
    $old_user=array(
      'id'=>43,
      'person_id'=>'def',
    );
    $user[43]=$old_user;
    
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $pm
      ->insert($user[42]);
    $pm
      ->insert($user[43]);
    $user[43]['person_id']='ghi';
    $pm
      ->save($user[43],$old_user);
    $pm
      ->delete_by_id(42);
    $this->assertSame(array(43=>$user[43]),$pm->multi_get_by_ids(array(42,43)));
  }
  public function testGetByIdSuccess(){
    $encoded = 'magic';
    $data = array(
      'id' => 42,
      'person_id' => 'abc',
    );
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('prefix/42'))
      ->will($this->returnValue($encoded));
    $pm = $this->getMock('SimpleCachePersistenceManager',array('decode_data'),array($this->getFieldsDescriptor(),$cache,'prefix'));
    $pm
      ->expects($this->once())
      ->method('decode_data')
      ->with($this->equalTo($encoded))
      ->will($this->returnValue($data)); 
    $this->assertEquals($data,$pm->get_by_id(42));
  }
  /**
   * @expectedException NoSuchEntityException
   */
  public function testGetByIdMiss(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('prefix/42'))
      ->will($this->throwException(new IsMissingException('prefix/42')));
    $pm = $this->getMock('SimpleCachePersistenceManager',array('decode_data'),array($this->getFieldsDescriptor(),$cache,'prefix'));
    $pm
      ->expects($this->never())
      ->method('decode_data');
    $pm->get_by_id(42);
  }
  /**
   * @expectedException NoSuchEntityException
   */
  public function testGetByIdCorruption(){
    $encoded = 'corruption';
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('prefix/42'))
      ->will($this->returnValue($encoded));
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $pm->get_by_id(42);
  }
  /**
   * @dataProvider getBool
   */
  public function testDeleteById($success){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('prefix/42'))
      ->will($this->returnValue($success));
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $this->assertSame($success,$pm->delete_by_id(42));
  }
  /**
   * @expectedException BadMethodCallException
   */
  public function testCannotAssignId(){
    $cache = $this->getMock('IPrefetchingCache');
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $pm->insert_and_assign_id(array('person_id'=>'abc'));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsert($success){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('prefix/42'),$this->equalTo('magic'))
      ->will($this->returnValue($success));
    $data=array(
      'id'=>42,
      'person_id'=>'abc',
    );
    $pm = $this->getMock('SimpleCachePersistenceManager',array('encode_data'),array($this->getFieldsDescriptor(),$cache,'prefix'));
    $pm
      ->expects($this->once())
      ->method('encode_data')
      ->with($this->equalTo($data))
      ->will($this->returnValue('magic'));
    $this->assertSame($success,$pm->insert($data));
  }
  public function testSave(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('prefix/42'),$this->equalTo('magic'));
    $data=array(
      'id'=>42,
      'person_id'=>'abc',
    );
    $old_data = array(
      'person_id' => 'def',
    );
    $pm = $this->getMock('SimpleCachePersistenceManager',array('encode_data'),array($this->getFieldsDescriptor(),$cache,'prefix'));
    $pm
      ->expects($this->once())
      ->method('encode_data')
      ->with($this->equalTo($data))
      ->will($this->returnValue('magic'));
    $pm->save($data,$old_data);
  }
  private function untouchableCache(){
    $cache = $this->getMock('IPrefetchingCache');
    foreach(array('set','get','add','delete','multi_get','prefetch') as $method){
      $cache
        ->expects($this->never())
        ->method($method);
    }
    return $cache;
  }
  /**
   * @dataProvider badChange
   * @expectedException IValidationException
   */
  public function testSaveValidatesNewData(array $current,array $original){
    $cache = $this->untouchableCache();
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $pm->save($current,$original);
  }
  public function badChange(){
    return array(
      array(array(),array()),
      array(array('id'=>42),array()),
      array(array('id'=>42,'person_id'=>'abc'),array('id'=>43)),
      array(array('id'=>42,'person_id'=>'abc'),array('id'=>42,'whatever'=>'x')),
      array(array('id'=>'whatever','person_id'=>'abc'),array()),
      array(array('id'=>42,'person_id'=>12),array()),
    );
  } 
  /**
   * @dataProvider noChanges
   */
  public function testSaveWithoutChanges(array $current,array $original){
    $cache = $this->untouchableCache();
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $pm->save($current,$original);
  }
  public function noChanges(){
    return array(
      array(array(
        'person_id' => 'abc',
        'id' => 42,
      ),array(
        'person_id' => 'abc',
      )),
      array(array(
        'person_id' => 'abc',
        'id' => 42,
      ),array(
      )),
      array(array(
        'person_id' => 'abc',
        'id' => 42,
      ),array(
        'id'=>42,
      )),
    );
  }
  public function badInsertData(){
    return array(
      array(array()),
      array(array('person_id')),
      array(array('person_id'=>13)),
      array(array('person_id'=>'abc','id'=>'13')),
      array(array('id'=>13)),
      array(array('person_id'=>'abc','whatever'=>'whatever')),
      array(array('person_id'=>'abc')),
      array(array('person_id'=>'abc','id'=>42,'whatever'=>'whatever')),
    );
  }

  /**
   * @dataProvider badInsertData
   * @expectedException IValidationException
   */
  public function testInsertValidatesData($bad_data){
    $cache = $this->untouchableCache();
    $pm = new SimpleCachePersistenceManager($this->getFieldsDescriptor(),$cache,'prefix');
    $pm->insert($bad_data);
  } 
}
?>
