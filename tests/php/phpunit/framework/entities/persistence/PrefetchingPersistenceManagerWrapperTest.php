<?php
class PrefetchingPersistenceManagerWrapperTest extends FrameworkTestCase
{
  public function testInterface(){
    $pm = $this->getMock('IPersistenceManager');
    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $this->assertInstanceOf('IPersistenceManager',$ppm);
    $this->assertInstanceOf('IPrefetchingPersistenceManager',$ppm);
    $this->assertInstanceOf('PersistenceManagerWrapper',$ppm);
  }
  public function testGetById(){
    $data=array('id'=>42,'x'=>'y');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(42))
      ->will($this->returnValue($data));
    $pm
      ->expects($this->never())
      ->method('multi_get_by_ids');

    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $this->assertSame($data,$ppm->get_by_id(42));
  }
  public function testMultiGetByIds(){
    $data=array(42=>array('id'=>42,'x'=>'y'));
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(42,43)))
      ->will($this->returnValue($data));
    $pm
      ->expects($this->never())
      ->method('get_by_id');

    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $this->assertEquals($data,$ppm->multi_get_by_ids(array(42,43)));
  }
  public function testPrefetchingGetById(){
    $data[1]=array('id'=>1,'x'=>'x');
    $data[3]=array('id'=>3,'x'=>'y');
    $data[5]=array('id'=>5,'x'=>'z');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array_keys($data)))
      ->will($this->returnValue(array(1=>$data[1],5=>$data[5])));
    $pm
      ->expects($this->never())
      ->method('get_by_id');

    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $ppm->prefetch_by_id(1); 
    $ppm->prefetch_by_id(3); 
    $ppm->prefetch_by_id(5); 
    $this->assertSame($data[1],$ppm->get_by_id(1));
  }
  /**
   * @expectedException NoSuchEntityException
   */
  public function testPrefetchingGetByIdMiss(){
    $data[1]=array('id'=>1,'x'=>'x');
    $data[3]=array('id'=>3,'x'=>'y');
    $data[5]=array('id'=>5,'x'=>'z');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array_keys($data)))
      ->will($this->returnValue(array(1=>$data[1],5=>$data[5])));
    $pm
      ->expects($this->never())
      ->method('get_by_id');

    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $ppm->prefetch_by_id(1); 
    $ppm->prefetch_by_id(3); 
    $ppm->prefetch_by_id(5); 
    $ppm->get_by_id(3);
  }
  public function testPrefetchingMultiGet(){
    $data[1]=array('id'=>1,'x'=>'x');
    $data[3]=array('id'=>3,'x'=>'y');
    $data[5]=array('id'=>5,'x'=>'z');
    $data[7]=array('id'=>7,'x'=>'u');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array_keys($data)))
      ->will($this->returnValue(array(1=>$data[1],5=>$data[5])));
    $pm
      ->expects($this->never())
      ->method('get_by_id');

    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $ppm->prefetch_by_id(1); 
    $ppm->prefetch_by_id(3); 
    $ppm->prefetch_by_id(5); 
    $this->assertEquals(array(5=>$data[5]),$ppm->multi_get_by_ids(array(5,7)));
  }
  public function testDontTriggerOnEveryGetById(){
    $data[7]=array('id'=>7,'x'=>'u');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->never())
      ->method('multi_get_by_ids');
    $pm
      ->expects($this->once())
      ->method('get_by_id')
      ->with($this->equalTo(7))
      ->will($this->returnValue($data[7]));

    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $ppm->prefetch_by_id(1); 
    $ppm->prefetch_by_id(3); 
    $ppm->prefetch_by_id(5); 
 
    $this->assertEquals($data[7],$ppm->get_by_id(7));
  }
  public function testDontTriggerOnEveryMultiGetByIds(){
    $data[5]=array('id'=>5,'x'=>'z');
    $pm = $this->getMock('IPersistenceManager');
    $pm
      ->expects($this->once())
      ->method('multi_get_by_ids')
      ->with($this->isPermutationOf(array(5,7)))
      ->will($this->returnValue(array(5=>$data[5])));
    $pm
      ->expects($this->never())
      ->method('get_by_id');
    $ppm = new PrefetchingPersistenceManagerWrapper($pm);
    $ppm->prefetch_by_id(1); 
    $ppm->prefetch_by_id(3); 
    $this->assertEquals(array(5=>$data[5]),$ppm->multi_get_by_ids(array(5,7)));
  }
 }
?>
