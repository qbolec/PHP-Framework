<?php
class CachedRelationManagerTest extends FrameworkTestCase
{
  public function getSUT(IRelationManager $relation){
    $cache = new PrefetchingCacheWrapper(new ArrayCache());
    $versioning = new CacheVersioning($cache,'test',array_keys($relation->get_fields_descriptor()->get_description()));
    return new CachedRelationManager($cache,$versioning,'test',$relation);
  }
  public function testInterface(){
    $r = $this->getSUT($this->getInnerRelationManager());
    $this->assertInstanceOf('IRelationManager',$r);
  }
  private function getInnerRelationManager(){
    $fields_descriptor = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
    ));
    $relation = $this->getMock('IRelationManager');
    $relation
      ->expects($this->any())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($fields_descriptor));
    return $relation;
  }
  public function testGetCount(){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_count')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));
  }
  public function testGetAll(){
    $key = array('a'=>13);
    $order_by = array('b'=>IRelationManager::DESC);
    $limit = 10;
    $offset = 20;
    $value = array(array('b'=>42));

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_all')
      ->with($this->equalTo($key),$this->equalTo($order_by),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_all($key,$order_by,$limit,$offset));
    $this->assertSame($value,$wrapped->get_all($key,$order_by,$limit,$offset));
  }
  public function testGetAllDefaultArgs(){
    $key = array('a'=>13);
    $order_by = array();
    $limit = null;
    $offset = null;
    $value = array(array('b'=>42));

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_all')
      ->with($this->equalTo($key),$this->equalTo($order_by),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_all($key));
    $this->assertSame($value,$wrapped->get_all($key));
  }
  public function testGetSingleColumn(){
    $key = array('a'=>13);
    $sorting_direction = IRelationManager::ASC;
    $limit = 10;
    $offset = 20;
    $value = array(42);

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_single_column')
      ->with($this->equalTo($key),$this->equalTo($sorting_direction),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_single_column($key,$sorting_direction,$limit,$offset));
    $this->assertSame($value,$wrapped->get_single_column($key,$sorting_direction,$limit,$offset));
  }
  public function testGetSingleColumnDefaultArgs(){
    $key = array('a'=>13);
    $sorting_direction = IRelationManager::DESC;
    $limit = null;
    $offset = null;
    $value = array(42);

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_single_column')
      ->with($this->equalTo($key),$this->equalTo($sorting_direction),$this->equalTo($limit),$this->equalTo($offset))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_single_column($key));
    $this->assertSame($value,$wrapped->get_single_column($key));
  }
  public function testGetSingleRow(){
    $key = array('a'=>13);
    $value = array('b'=>42);

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_single_row')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_single_row($key));
    $this->assertSame($value,$wrapped->get_single_row($key));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsert($value){
    $key = array('a'=>13,'b'=>42);

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->insert($key));
  }
  public function testDeleteMultipleRowsDoesNotIterate(){
    $key = array('a'=>13);

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->never())
      ->method('get_all');
    $relation
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo(array('a'=>13)))
      ->will($this->returnValue(2));

    $wrapped = $this->getSUT($relation);
    $this->assertSame(2,$wrapped->delete($key));
  }
  public function testDelete(){
    $key = array('a'=>13,'b'=>42);
    $value = 1;

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->delete($key));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsertInvalidatesGetCount($modified){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->exactly($modified?2:1))
      ->method('get_count')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));

    $new_data =array('a'=>13,'b'=>42);
    $relation
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($new_data))
      ->will($this->returnValue($modified));

    $this->assertSame($modified,$wrapped->insert($new_data));
    

    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));
  }
  public function testInsertOtherDoesNotInvalidateGetCount(){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_count')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));

    $new_data =array('a'=>69,'b'=>42);
    $modified = true;
    $relation
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($new_data))
      ->will($this->returnValue($modified));

    $this->assertSame($modified,$wrapped->insert($new_data));
    

    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));
  }
  /**
   * @dataProvider getBool
   */
  public function testDeleteInvalidatesGetCount($modified){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->exactly($modified?2:1))
      ->method('get_count')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));

    $new_data =array('a'=>13,'b'=>42);
    $deleted = $modified?1:0;
    $relation
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo($new_data))
      ->will($this->returnValue($deleted));

    $this->assertSame($deleted,$wrapped->delete($new_data));
    

    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));
  }
  public function testDeleteOtherDoesNotInvalidateGetCount(){
    $key = array('a'=>13);
    $value = 42;

    $relation = $this->getInnerRelationManager();
    $relation
      ->expects($this->once())
      ->method('get_count')
      ->with($this->equalTo($key))
      ->will($this->returnValue($value));

    $wrapped = $this->getSUT($relation);
    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));

    $new_data =array('a'=>69,'b'=>42);
    $deleted = 1;
    $relation
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo($new_data))
      ->will($this->returnValue($deleted));

    $this->assertSame($deleted,$wrapped->delete($new_data));
    

    $this->assertSame($value,$wrapped->get_count($key));
    $this->assertSame($value,$wrapped->get_count($key));
  }

}
?>
