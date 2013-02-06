<?php
class SimplePDOPersistenceManagerTest extends FrameworkTestCase
{
  private function getFieldsDescriptor(){
    return $this->getUserlikeFieldsDescriptor();
  }
  private function sharding(){
    //@todo: coś jest bardzo nie tak z PHPUnit, i nie mogę łączyć any() z with(), bo wyskakuje Mocked method does not exists.
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->any())
      ->method('get_shard_id_from_entity_id')
      //->with($this->equalTo(1),$this->isType('array'))
      ->will($this->returnValue(0));
    $sharding
      ->expects($this->any())
      ->method('get_shard_id_from_data_without_id')
      //->with($this->equalTo(1),$this->isType('array'))
      ->will($this->returnValue(0));
    return $sharding;
  }
  public function testInterface(){
    $sharding = $this->getMock('ISharding');
    $pm = new SimplePDOPersistenceManager($this->getFieldsDescriptor(),$sharding,'x','x');
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
  public function testDeleteById(){
    $id = 42;
    $q = $this->getMock('IPDOStatement');
    $q
      ->expects($this->once())
      ->method('bindValue')
      ->with($this->equalTo(':id'),$this->equalTo(42),$this->equalTo(PDO::PARAM_INT));
    $q
      ->expects($this->once())
      ->method('execute');
    $q
      ->expects($this->once())
      ->method('rowCount')
      ->will($this->returnValue(1));

    $pdo = $this->getMock('IPDO');
    $pdo
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/DELETE\s+FROM.*x_table.*WHERE.*id/'))
      ->will($this->returnValue($q));
    $pdos = $this->getMock('IPDOFactory');
    $pdos
      ->expects($this->once())
      ->method('get_shards_count')
      ->with($this->equalTo('x_db'))
      ->will($this->returnValue(10));
    $pdos
      ->expects($this->once())
      ->method('get_pdo')
      ->with($this->equalTo('x_db'),$this->equalTo(2))
      ->will($this->returnValue($pdo));


    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->once())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->equalTo(42))
      ->will($this->returnValue(2)); 
    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'x_db','x_table'));
    $pm
      ->expects($this->any())
      ->method('get_pdo_factory')
      ->will($this->returnValue($pdos));
    $this->assertSame(true,$pm->delete_by_id($id));
  }
  public function testGetById(){
    $q = $this->getMock('IPDOStatement');
    $q
      ->expects($this->once())
      ->method('bindValue')
      ->with($this->equalTo(':id'),$this->equalTo(42),$this->equalTo(PDO::PARAM_INT));
    $q
      ->expects($this->once())
      ->method('execute');
    $q
      ->expects($this->once())
      ->method('fetch')
      ->will($this->returnValue(array(
        'id' => '42',
        'person_id' => 'abc',
      )));
    $pdo = $this->getMock('IPDO');
    $pdo
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/SELECT.*FROM.*x.*WHERE.*id/'))
      ->will($this->returnValue($q));
    $pdos = $this->getMock('IPDOFactory');
    $pdos
      ->expects($this->once())
      ->method('get_shards_count')
      ->with($this->equalTo('users'))
      ->will($this->returnValue(10));
    $pdos
      ->expects($this->once())
      ->method('get_pdo')
      ->with($this->equalTo('users'),$this->equalTo(2))
      ->will($this->returnValue($pdo));


    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->once())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->equalTo(42))
      ->will($this->returnValue(2)); 

    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'users','x'));
    $pm
      ->expects($this->any())
      ->method('get_pdo_factory')
      ->will($this->returnValue($pdos));
    $u = $pm->get_by_id(42);
    $this->assertInternalType('array',$u);
    $this->assertSame(42,$u['id']);
    $this->assertSame('abc',$u['person_id']);
  }
  public function badInsertAndAssignIdData(){
    return array(
      array(array()),
      array(array('person_id')),
      array(array('person_id'=>13)),
      array(array('person_id'=>'abc','id'=>13)),
      array(array('id'=>13)),
      array(array('person_id'=>'abc','whatever'=>'whatever')),
    );
  }

  /**
   * @dataProvider badInsertAndAssignIdData
   * @expectedException IValidationException
   */
  public function testInsertAndAssignIdValidatesData($bad_data){
    
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->never())
      ->method('get_shard_id_from_entity_id');
    $sharding
      ->expects($this->never())
      ->method('get_shard_id_from_data_without_id');

 
    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'users','x'));
    $pm
      ->expects($this->never())
      ->method('get_pdo_factory');
 
    $id = $pm->insert_and_assign_id($bad_data);
  }
  public function testInsertAndAssignId(){
    $q = $this->getMock('IPDOStatement');
    $q
      ->expects($this->once())
      ->method('bindValues')
      ->with($this->anything(),$this->equalTo(array('person_id'=>'abc')));
    $q
      ->expects($this->once())
      ->method('execute');
    $pdo = $this->getMock('IPDO');
    $pdo
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/INSERT.*INTO.*x.*person_id.*VALUES.*person_id/'))
      ->will($this->returnValue($q));
    $pdo
      ->expects($this->once())
      ->method('lastInsertId')
      ->will($this->returnValue('42'));

    $pdos = $this->getMock('IPDOFactory');
    $pdos
      ->expects($this->once())
      ->method('get_shards_count')
      ->with($this->equalTo('users'))
      ->will($this->returnValue(1));
    $pdos
      ->expects($this->once())
      ->method('get_pdo')
      ->with($this->equalTo('users'),$this->equalTo(0))
      ->will($this->returnValue($pdo));

    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$this->sharding(),'users','x'));
    $pm
      ->expects($this->any())
      ->method('get_pdo_factory')
      ->will($this->returnValue($pdos));

    $id = $pm->insert_and_assign_id(array(
      'person_id' => 'abc',
    ));
    $this->assertSame(42,$id);
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
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->never())
      ->method('get_shard_id_from_entity_id');
    $sharding
      ->expects($this->never())
      ->method('get_shard_id_from_data_without_id');

    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'users','x'));
    $pm
      ->expects($this->never())
      ->method('get_pdo_factory');
    $id = $pm->insert($bad_data);
  }
  /**
   * @dataProvider getBool
   */
  public function testInsert($success){
    $q = $this->getMock('IPDOStatement');
    $q
      ->expects($this->once())
      ->method('bindValues');
    $q
      ->expects($this->once())
      ->method('rowCount')
      ->will($this->returnValue($success));
    $pdo = $this->getMock('IPDO');
    $pdo
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/INSERT IGNORE INTO.*x.*person_id.*VALUES.*person_id/'))
      ->will($this->returnValue($q));
    $pdo
      ->expects($this->never())
      ->method('lastInsertId');
    $pdo
      ->expects($this->once())
      ->method('insert_ignore_command')
      ->will($this->returnValue('INSERT IGNORE'));
    $pdos = $this->getMock('IPDOFactory');
    $pdos
      ->expects($this->once())
      ->method('get_shards_count')
      ->with($this->equalTo('users'))
      ->will($this->returnValue(1));
    $pdos
      ->expects($this->once())
      ->method('get_pdo')
      ->with($this->equalTo('users'),$this->equalTo(0))
      ->will($this->returnValue($pdo));

    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$this->sharding(),'users','x'));
    $pm
      ->expects($this->any())
      ->method('get_pdo_factory')
      ->will($this->returnValue($pdos));
    $this->assertSame($success,$pm->insert(array(
      'person_id' => 'abc',
      'id' => 42,
    )));
  }
  public function testSave(){
    $q = $this->getMock('IPDOStatement');
    $q
      ->expects($this->once())
      ->method('bindValues');
    $q
      ->expects($this->once())
      ->method('rowCount')
      ->will($this->returnValue(1));
    $pdo = $this->getMock('IPDO');
    $pdo
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/UPDATE.*x.*SET.*person_id.*=.*WHERE.*id/'))
      ->will($this->returnValue($q));
    $pdos = $this->getMock('IPDOFactory');
    $pdos
      ->expects($this->once())
      ->method('get_shards_count')
      ->with($this->equalTo('users'))
      ->will($this->returnValue(10));
    $pdos
      ->expects($this->once())
      ->method('get_pdo')
      ->with($this->equalTo('users'),$this->equalTo(2))
      ->will($this->returnValue($pdo));
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->once())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->equalTo(42))
      ->will($this->returnValue(2)); 
 
    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'users','x'));
    $pm
      ->expects($this->any())
      ->method('get_pdo_factory')
      ->will($this->returnValue($pdos));
    $this->assertSame(true,$pm->save(array(
      'person_id' => 'abc',
      'id' => 42,
    ),array(
      'person_id' => 'xyz',
    )));
  }
  /**
   * @dataProvider noChanges
   */
  public function testSaveWithoutChanges(array $current,array $original){
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->never())
      ->method('get_shard_id_from_entity_id');
    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'users','x'));
    $pm
      ->expects($this->never())
      ->method('get_pdo_factory');
    $this->assertSame(false,$pm->save($current,$original));
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
  /**
   * @dataProvider badChange
   * @expectedException IValidationException
   */
  public function testSaveValidatesNewData(array $current,array $original){
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->never())
      ->method('get_shard_id_from_entity_id');
    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'users','x'));
    $pm
      ->expects($this->never())
      ->method('get_pdo_factory');
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
  public function testMultiGetById(){
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->any())
      ->method('get_shard_id_from_entity_id')
      ->with($this->equalTo(10),$this->anything())
      ->will($this->returnCallback(function($shard_count,$id){
        return $id%2;
      }));
    for($i=0;$i<2;++$i){
      $q[$i] = $this->getMock('IPDOStatement');
      $q[$i]
        ->expects($this->never())
        ->method('bindValue');
      $q[$i]
        ->expects($this->never())
        ->method('bindValues');
      $q[$i]
        ->expects($this->once())
        ->method('execute');
    }
    $q[0]
      ->expects($this->exactly(3))
      ->method('fetch')
      ->will($this->onConsecutiveCalls(
        array('id'=>12,'person_id'=>'abc'),
        array('id'=>16,'person_id'=>'def'),
        false
      ));
    $q[1]
      ->expects($this->exactly(2))
      ->method('fetch')
      ->will($this->onConsecutiveCalls(
        //missing: array('id'=>13,'person_id'=>'xyz'),
        array('id'=>17,'person_id'=>'ghi'),
        false
      ));
    $pdo[0] = $this->getMock('IPDO');
    $pdo[0]
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/SELECT.*FROM.*x_table.*WHERE.*id.*IN.*\\(((12.*,.*16)|(16.*,.*12))\\)/'))
      ->will($this->returnValue($q[0]));
    $pdo[1] = $this->getMock('IPDO');
    $pdo[1]
      ->expects($this->once())
      ->method('prepare')
      ->with($this->matchesRegularExpression('/SELECT.*FROM.*x_table.*WHERE.*id.*IN.*\\(((13.*,.*17)|(17.*,.*13))\\)/'))
      ->will($this->returnValue($q[1]));

    $pdos = $this->getMock('IPDOFactory');
    $pdos
      ->expects($this->any())
      ->method('get_shards_count')
      ->with($this->equalTo('x_db'))
      ->will($this->returnValue(10));
    $pdos
      ->expects($this->exactly(2))
      ->method('get_pdo')
      ->with($this->equalTo('x_db'),$this->anything())
      ->will($this->returnCallback(function($pdo_name,$pdo_index)use($pdo){
        return $pdo[$pdo_index];
      }));

    $pm = $this->getMock('SimplePDOPersistenceManager',array('get_pdo_factory'),array($this->getFieldsDescriptor(),$sharding,'x_db','x_table'));
    $pm
      ->expects($this->any())
      ->method('get_pdo_factory')
      ->will($this->returnValue($pdos));
    $users =$pm->multi_get_by_ids(array(17,16,12,13));
    ksort($users); 
    $this->assertEquals(array(
      12=>array(
        'id'=>12,
        'person_id'=>'abc',
      ),
      16=>array(
        'id'=>16,
        'person_id'=>'def',
      ),
      17=>array(
        'id'=>17,
        'person_id'=>'ghi',
      ),
    ),$users);
  }
}
?>
