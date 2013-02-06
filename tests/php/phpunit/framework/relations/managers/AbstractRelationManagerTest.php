<?php
class AbstractRelationManagerTest extends FrameworkTestCase
{
  private function getFD(){
   return FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
      'c' => new StringFieldType(),
    ));
  }
  private function getSUT(){
    $fields_descriptor = $this->getFD();
    return $this->getMockForAbstractClass('AbstractRelationManager',array($fields_descriptor));
  }
  public function testInterface(){
    $r = $this->getSUT();
    $this->assertInstanceOf('IRelationManager',$r);
  }
  public function testGetSingleRowDuplication(){
    $fields_descriptor = $this->getMock('IFieldsDescriptor');
    $assertions = $this->getMock('IAssertions');
    $assertions
      ->expects($this->once())
      ->method('warn_if')
      ->with($this->equalTo(true));
    $r = $this->getMock('AbstractRelationManager',array('get_all','get_assertions','prevalidated_get_count','prevalidated_get_all','prevalidated_insert','prevalidated_delete'),array($fields_descriptor));
    $r
      ->expects($this->once())
      ->method('get_all')
      ->will($this->returnValue(array(array('c'=>'h'),array('c'=>'i'))));
    $r
      ->expects($this->any())
      ->method('get_assertions')
      ->will($this->returnValue($assertions));
    $r->get_single_row(array('a'=>1,'b'=>13));
  }
  /**
   * @expectedException LogicException
   */
  public function testGetSingleColumnTooMany(){
    $r = $this->getSUT();
    $r->get_single_column(array('b'=>1));
  }
  /**
   * @expectedException UnexpectedMemberException
   */
  public function testDeleteUnexpected(){
    $r = $this->getSUT();
    $r->delete(array('b'=>1,'whatever'=>13));
  }
  /**
   * @expectedException UnexpectedMemberException
   */
  public function testGetCountUnexpected(){
    $r = $this->getSUT();
    $r->get_count(array('b'=>1,'whatever'=>13));
  }
  /**
   * @dataProvider getCColumn
   */
  public function testGetSingleColumnForwards($data){
    $fields_description = $this->getFD()->get_description();
    $key = array('a'=>1,'b'=>1);
    $key_description = array_intersect_key($fields_description,$key);
    $sorting_direction = IRelationManager::ASC;
    $limit = 13;
    $offset = 42;
    $r = $this->getSUT();
    $r
      ->expects($this->once())
      ->method('prevalidated_get_all')
      ->with($this->equalTo($key),$this->equalTo(array('c'=>$sorting_direction)),$this->equalTo($limit),$this->equalTo($offset),$this->equalTo($key_description),$this->equalTo($fields_description))
      ->will($this->returnValue($data));
    $this->assertSame(Arrays::get(Arrays::transpose($data),'c',array()),$r->get_single_column($key,$sorting_direction,$limit,$offset));
  }
  public function getCColumn(){
    return array(
      array(array(array('c'=>'h'),array('c'=>'i'))),
      array(array()),
    );
  }
  public function testGetCountForwards(){
    $fields_description = $this->getFD()->get_description();
    $key = array('a'=>1,'b'=>1);
    $key_description = array_intersect_key($fields_description,$key);
    $count = 13;
    $r = $this->getSUT();
    $r
      ->expects($this->once())
      ->method('prevalidated_get_count')
      ->with($this->equalTo($key_description),$this->equalTo($key))
      ->will($this->returnValue($count));
    $this->assertSame($count,$r->get_count($key));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsertForwards($added){
    $fields_description = $this->getFD()->get_description();
    $data = array('a'=>1,'b'=>1,'c'=>'x');
    $r = $this->getSUT();
    $r
      ->expects($this->once())
      ->method('prevalidated_insert')
      ->with($this->equalTo($fields_description),$this->equalTo($data))
      ->will($this->returnValue($added));
    $this->assertSame($added,$r->insert($data));
  }
  public function testDeleteForwards(){
    $fields_description = $this->getFD()->get_description();
    $key = array('a'=>1,'b'=>1);
    $key_description = array_intersect_key($fields_description,$key);
    $deleted = 13;
    $r = $this->getSUT();
    $r
      ->expects($this->once())
      ->method('prevalidated_delete')
      ->with($this->equalTo($key_description),$this->equalTo($key))
      ->will($this->returnValue($deleted));
    $this->assertSame($deleted,$r->delete($key));
  }


}
?>
