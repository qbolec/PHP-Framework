<?php
class SimplePDOPersistenceManagerIntegrationTest extends FrameworkTestCase
{
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
  private function config(){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(
        array(
          'logging' => array(
            'rules' => array(
            ),
          ),
          'pdos' => array(
            'masters' => array(
              'users' => array(
                0 => $this->get_test_pdo_config(),
              ),
              'sharded-users' => array(
                0 => $this->get_test_pdo_config(),
                1 => $this->get_test_pdo_config(),
              ),

            ),
          ),
        )
      ));
    return $config; 
  }
  private function getSUT(){
    return new SimplePDOPersistenceManager($this->getUserlikeFieldsDescriptor(),$this->sharding(),'users','users_fake');
  }
  public function testGetByIdWithDatabase(){
    $this->set_global_mock('Config',$this->config());
    $pm = $this->getSUT(); 
    $u = $pm->get_by_id(42);
    $this->assertInternalType('array',$u);
    $this->assertSame(42,$u['id']);
    $this->assertSame('cafebabe',$u['person_id']);

  }
  public function testMultiGetByIdsWithDatabase(){
    $this->set_global_mock('Config',$this->config());
    $sharding = $this->getMock('ISharding');
    $sharding
      ->expects($this->any())
      ->method('get_shard_id_from_entity_id')
      //->with($this->equalTo(1),$this->isType('array'))
      ->will($this->returnCallback(function($shards_count,$id){return $id%2;}));
 
    $pm = new SimplePDOPersistenceManager($this->getUserlikeFieldsDescriptor(),$sharding,'sharded-users','users_fake');
    $users = $pm->multi_get_by_ids(array(42,43,44));
    
    $this->assertInternalType('array',$users);
    ksort($users);
    $this->assertSame(array(
      42 => array(
        'id' => 42,
        'person_id' => 'cafebabe',
      ),
      43 => array(
        'id' => 43,
        'person_id' => 'cafe',
      ),
      44 => array(
        'id' => 44,
        'person_id' => 'babe',
      ),
    ),$users);
  }
  public function testDeleteByIdWithDatabase(){
    $original_config = Config::set_instance($this->config());
    $pm = $this->getSUT(); 
    $u = $pm->delete_by_id(400000000);
    $this->assertSame(false,$u);
    Config::set_instance($original_config);
  }
  public function testInsertAndDeleteWithDatabase(){
    $original_config = Config::set_instance($this->config());
    $pm = $this->getSUT(); 
    $pm->insert(array(
      'id'=>13,
      'person_id'=>'test-left-over',
    ));
    $this->assertTrue($pm->delete_by_id(13)); 
    $this->assertFalse($pm->delete_by_id(13)); 
    Config::set_instance($original_config);
  }
  public function testInsertAndAssignIdAndDeleteWithDatabase(){
    $original_config = Config::set_instance($this->config());
    $pm = $this->getSUT(); 
    $id = $pm->insert_and_assign_id(array(
      'person_id'=>'test-left-over',
    ));
    $this->assertInternalType('int',$id);
    $this->assertTrue($pm->delete_by_id($id)); 
    Config::set_instance($original_config);
    return $id;
  }
  public function testInsertSaveGetDeleteWithDatabase(){
    $this->set_global_mock('Config',$this->config());
    $pm = $this->getSUT(); 
    $old_data = array(
      'person_id'=>'test-left-over',
    );
    $id = $pm->insert_and_assign_id($old_data);
    $this->assertInternalType('int',$id);
    $new_data =array('id'=>$id,'person_id'=>'akuku');
    $this->assertSame(true,$pm->save($new_data,$old_data));
    $this->assertSame($new_data,$pm->get_by_id($id));
    $this->assertTrue($pm->delete_by_id($id)); 
    return $id;
  }
  /**
   * @depends testInsertAndAssignIdAndDeleteWithDatabase
   * @expectedException NoSuchEntityException
   */
  public function testNoLeftOvers($id){
    $this->set_global_mock('Config',$this->config());
    $pm = $this->getSUT(); 
    $pm->get_by_id($id);
  }
}
?>
