<?php
class MergedPersistenceManagerTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = $this->getMock('IPersistenceManager');
    $b = $this->getMock('IPersistenceManager');
    $pm = new MergedPersistenceManager($a,$b);
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
  private function getSUT(){
    $b = $this->getMock('IPersistenceManager');
    $e = $this->getMock('IPersistenceManager');
    $pm = new MergedPersistenceManager($b,$e);
    $logger = $this->getMock('ILogger');
    $framework = $this->getMock('IFramework');
    $framework
      ->expects($this->any())
      ->method('get_logger')
      ->will($this->returnValue($logger));

    $this->set_global_mock('Framework',$framework);
    return array($pm,$b,$e,$logger); 
  }
  /**
   * @expectedException NoSuchEntityException
   */
  public function testGetByIdBaseMiss(){
    list($pm,$b,$e,$logger) = $this->getSUT();
    $b
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(13))
      ->will($this->throwException(new NoSuchEntityException(13)));
    $e
      ->expects($this->never())
      ->method('get_by_id');
    $logger
      ->expects($this->never())
      ->method('log');
    $pm->get_by_id(13);
  }
  /**
   * @expectedException NoSuchEntityException
   */
  public function testGetByIdExtensionMiss(){
    $data = array('id'=>13);
    list($pm,$b,$e,$logger) = $this->getSUT();
    $b
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(13))
      ->will($this->returnValue($data));
    $e
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(13))
      ->will($this->throwException(new NoSuchEntityException(13)));
    $logger
      ->expects($this->once())
      ->method('log');
    $pm->get_by_id(13);
  }
  public function testGetByIdSuccess(){
    $data_b = array('id'=>13,'a'=>'b');
    $data_e = array('id'=>13,'x'=>'y');
    list($pm,$b,$e,$logger) = $this->getSUT();
    $b
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(13))
      ->will($this->returnValue($data_b));
    $e
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(13))
      ->will($this->returnValue($data_e));
    $logger
      ->expects($this->never())
      ->method('log');
    $this->assertEquals(array_merge($data_b,$data_e),$pm->get_by_id(13));
  }
  public function getBoolBool(){
    return array(
      array(false,false),
      array(false,true),
      array(true,false),
      array(true,true),
    );
  }
  /**
   * @dataProvider getBoolBool
   */
  public function testDeleteById($base_success,$extension_success){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $b
      ->expects($this->once())
      ->method('delete_by_id')
      ->with($this->equalTo(13))
      ->will($this->returnValue($base_success));
    $e
      ->expects($this->once())
      ->method('delete_by_id')
      ->with($this->equalTo(13))
      ->will($this->returnValue($extension_success));
    $logger
      ->expects($base_success==$extension_success?$this->never():$this->once())
      ->method('log');
    $this->assertSame($base_success,$pm->delete_by_id(13));
  } 
  private function assign_fields_descriptors($b,$e){
    $bfd = $this->getMock('IFieldsDescriptor');
    $bfd
      ->expects($this->any())
      ->method('get_description')
      ->will($this->returnValue(array('id'=>new IntFieldType(),'b'=>new StringFieldType())));
    $efd = $this->getMock('IFieldsDescriptor');
    $efd
      ->expects($this->any())
      ->method('get_description')
      ->will($this->returnValue(array('id'=>new IntFieldType(),'e'=>new StringFieldType())));
    $b
      ->expects($this->any())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($bfd));
    $e
      ->expects($this->any())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($efd));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsertAndAssignId($extension_success){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $b
      ->expects($this->once())
      ->method('insert_and_assign_id')
      ->with($this->equalTo(array('b'=>'B')))
      ->will($this->returnValue(13));
    $e
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo(array('id'=>13,'e'=>'E')))
      ->will($this->returnValue($extension_success));
    $logger
      ->expects($extension_success?$this->never():$this->once())
      ->method('log');
    $this->assertSame(13,$pm->insert_and_assign_id(array('b'=>'B','e'=>'E')));
  } 
  /**
   * @dataProvider getBool
   */
  public function testInsert($extension_success){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $b
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo(array('id'=>13,'b'=>'B')))
      ->will($this->returnValue(true));
    $e
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo(array('id'=>13,'e'=>'E')))
      ->will($this->returnValue($extension_success));
    $logger
      ->expects($extension_success?$this->never():$this->once())
      ->method('log');
    $this->assertSame(true,$pm->insert(array('id'=>13,'b'=>'B','e'=>'E')));
  } 
  public function testInsertBaseDuplication(){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $b
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo(array('id'=>13,'b'=>'B')))
      ->will($this->returnValue(false));
    $e
      ->expects($this->never())
      ->method('insert');
    $logger
      ->expects($this->never())
      ->method('log');
    $this->assertSame(false,$pm->insert(array('id'=>13,'b'=>'B','e'=>'E')));
  } 
  public function testSave(){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $b
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo(array('id'=>13,'b'=>'B')),$this->equalTo(array('id'=>13,'b'=>'BB')));
    $e
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo(array('id'=>13,'e'=>'E')),$this->equalTo(array('id'=>13,'e'=>'EE')));
    $logger
      ->expects($this->never())
      ->method('log');
    $pm->save(array('id'=>13,'b'=>'B','e'=>'E'),array('id'=>13,'b'=>'BB','e'=>'EE'));
  } 
  public function testMultiGetByIdsDesynch(){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $b
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,44,13)))
      ->will($this->returnValue(array(
        42=>array('id'=>42,'b'=>'B'),
        44=>array('id'=>44,'b'=>'BB'),
      )));
    $e
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,44)))
      ->will($this->returnValue(array(
        42=>array('id'=>42,'e'=>'E'),
      )));
    $logger
      ->expects($this->once())
      ->method('log');
    $this->assertEquals(array(42=>array('id'=>42,'b'=>'B','e'=>'E')),$pm->multi_get_by_ids(array(13,42,44)));
  }
  public function testMultiGetByIds(){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $b
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,44,13)))
      ->will($this->returnValue(array(
        42=>array('id'=>42,'b'=>'B'),
        44=>array('id'=>44,'b'=>'BB'),
      )));
    $e
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,44)))
      ->will($this->returnValue(array(
        42=>array('id'=>42,'e'=>'E'),
        44=>array('id'=>44,'e'=>'EE'),
      )));
    $logger
      ->expects($this->never())
      ->method('log');
    $this->assertEquals(array(42=>array('id'=>42,'b'=>'B','e'=>'E'),44=>array('id'=>44,'b'=>'BB','e'=>'EE')),$pm->multi_get_by_ids(array(13,42,44)));
  }
  public function testGetFieldsDescriptor(){
    list($pm,$b,$e,$logger)=$this->getSUT();
    $this->assign_fields_descriptors($b,$e);
    $this->assertEquals(array('id'=>new IntFieldType(),'b'=>new StringFieldType(),'e'=>new StringFieldType()),$pm->get_fields_descriptor()->get_description()); 
  }   
}
?>
