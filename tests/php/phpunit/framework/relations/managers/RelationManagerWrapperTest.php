<?php
class RelationManagerWrapperTest extends FrameworkTestCase
{
  public function getSUT(IRelationManager $relation){
    return new RelationManagerWrapper($relation);
  }
  public function testInterface(){
    $r = $this->getSUT($this->getMock('IRelationManager'));
    $this->assertInstanceOf('IRelationManager',$r);
  }
  public function testGetCount(){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_count')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_count($key));
  }
  public function testGetFieldsDescriptor(){
    $fields_descriptor = $this->getMock('IFieldsDescriptor');
    
    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($fields_descriptor));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($fields_descriptor,$wrapped->get_fields_descriptor());
  }
  public function testGetAll(){
    $key = array('a'=>13);
    $order_by = array('b'=>IRelationManager::DESC);
    $limit = 10;
    $offset = 20;
    $value = array(array('b'=>42));

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_all')
      ->with($this->equalTo($key),$this->equalTo($order_by),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_all($key,$order_by,$limit,$offset));
  }
  public function testGetAllDefaultArgs(){
    $key = array('a'=>13);
    $order_by = array();
    $limit = null;
    $offset = null;
    $value = array(array('b'=>42));

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_all')
      ->with($this->equalTo($key),$this->equalTo($order_by),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_all($key));
  }
  public function testGetSingleColumn(){
    $key = array('a'=>13);
    $sorting_order = IRelationManager::ASC;
    $limit = 10;
    $offset = 20;
    $value = array(42);

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_single_column')
      ->with($this->equalTo($key),$this->equalTo($sorting_order),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_single_column($key,$sorting_order,$limit,$offset));
  }
  public function testGetSingleColumnDefaultArgs(){
    $key = array('a'=>13);
    $sorting_order = IRelationManager::DESC;
    $limit = null;
    $offset = null;
    $value = array(42);

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_single_column')
      ->with($this->equalTo($key),$this->equalTo($sorting_order),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_single_column($key));
  }
  public function testGetSingleRow(){
    $key = array('a'=>13);
    $value = array('b'=>42);

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('get_single_row')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_single_row($key));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsert($value){
    $key = array('a'=>13);

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->insert($key));
  }
  public function testDelete(){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->delete($key));
  }
 




}
?>
