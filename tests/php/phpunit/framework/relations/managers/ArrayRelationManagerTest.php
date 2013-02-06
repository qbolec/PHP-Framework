<?php
class ArrayRelationManagerTest extends FrameworkTestCase
{
  private function getSUT(){
    $fields_descriptor = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
      'c' => new StringFieldType(),
    ));
    $data = array(
      array('a'=>1,'b'=>1,'c'=>'x'),
      array('a'=>1,'b'=>1,'c'=>'y'),
      array('a'=>1,'b'=>1,'c'=>'z'),
      array('a'=>2,'b'=>1,'c'=>'x'),
      array('a'=>2,'b'=>2,'c'=>'x'),
      array('a'=>3,'b'=>4,'c'=>'z'),
    );
    $unique = array(array('a','b','c'));
    return new ArrayRelationManager($fields_descriptor,$data,$unique);
  }
  public function testInterface(){
    $r = $this->getSUT();
    $this->assertInstanceOf('IRelationManager',$r);
  }
  /**
   * @expectedException IsMissingException
   */
  public function testGetSingleRowMiss(){
    $r = $this->getSUT();
    $r->get_single_row(array('a'=>1,'b'=>13,'c'=>'h'));
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
   * @dataProvider areGetCount
   */
  public function testGetCount($data,$expected){
    $r = $this->getSUT();
    $this->assertSame($expected,$r->get_count($data));
  }
  public function areGetCount(){
    return array(
      array(array('a'=>1,'b'=>1,'c'=>'x'),1),
      array(array('a'=>1,'b'=>1),3),
      array(array('b'=>1),4),
      array(array('a'=>1,'b'=>3),0),
    );
  }
  /**
   * @dataProvider getAllData
   */
  public function testGetAll(array $key,array $order_by,$limit,$offset,$expected){
    $r = $this->getSUT();
    $this->assertSame($expected,$r->get_all($key,$order_by,$limit,$offset));
  }
  public function getAllData(){
    return array(
      array(array('b'=>1),array('a'=>IRelationManager::ASC,'b'=>IRelationManager::ASC,'c'=>IRelationManager::ASC),0,0,array()),
      array(array('b'=>1),array('a'=>IRelationManager::ASC,'b'=>IRelationManager::ASC,'c'=>IRelationManager::ASC),1,0,array(array('a'=>1,'c'=>'x'))),
      array(array('b'=>1),array('a'=>IRelationManager::ASC,'b'=>IRelationManager::ASC,'c'=>IRelationManager::ASC),1,1,array(array('a'=>1,'c'=>'y'))),
      array(array('b'=>1),array('c'=>IRelationManager::ASC,'b'=>IRelationManager::ASC,'a'=>IRelationManager::ASC),1,1,array(array('a'=>2,'c'=>'x'))),
      array(array('b'=>1),array('a'=>IRelationManager::DESC,'b'=>IRelationManager::DESC),1,0,array(array('a'=>2,'c'=>'x'))),
      array(array('b'=>1),array('a'=>IRelationManager::ASC,'b'=>IRelationManager::ASC,'c'=>IRelationManager::ASC),2,0,array(array('a'=>1,'c'=>'x'),array('a'=>1,'c'=>'y'))),
      array(array('b'=>1),array('c'=>IRelationManager::ASC,'a'=>IRelationManager::ASC),2,0,array(array('a'=>1,'c'=>'x'),array('a'=>2,'c'=>'x'))),
      array(array('b'=>1,'c'=>'h'),array('c'=>IRelationManager::ASC,'a'=>IRelationManager::ASC),2,0,array()),
      array(array('b'=>1,'c'=>'x'),array('c'=>IRelationManager::ASC,'a'=>IRelationManager::ASC),2,0,array(array('a'=>1),array('a'=>2))),
      array(array('a'=>1,'b'=>1,'c'=>'x'),array('c'=>IRelationManager::ASC,'a'=>IRelationManager::ASC),2,0,array(array())),
      array(array('a'=>1,'b'=>1,'c'=>'x'),array(),2,0,array(array())),
      array(array('a'=>1,'b'=>1,'c'=>'x'),array(),null,null,array(array())),
    );
  }
  public function testInsertDelete(){
    $r = $this->getSUT();
    $this->assertSame(true,$r->insert(array('a'=>1,'b'=>1,'c'=>'delete-me'))); 
    $this->assertSame(false,$r->insert(array('a'=>1,'b'=>1,'c'=>'delete-me'))); 
    $this->assertSame(1,$r->delete(array('a'=>1,'b'=>1,'c'=>'delete-me'))); 
    $this->assertSame(0,$r->delete(array('b'=>1,'c'=>'delete-me'))); 
  }
  /**
   * @dataProvider getSingleRowData
   */
  public function testGetSingleRow(array $key,$expected){
    $r = $this->getSUT();
    $this->assertSame($expected,$r->get_single_row($key));
  }
  public function getSingleRowData(){
    return array(
      array(array('b'=>4,'a'=>3),array('c'=>'z')),
      array(array('b'=>4),array('a'=>3,'c'=>'z')),
      array(array('b'=>4,'a'=>3,'c'=>'z'),array()),
    );
  }
  /**
   * @dataProvider getSingleColumnData
   */
  public function testGetSingleColumn(array $key, $sorting_direction,$limit,$offset,$expected){
    $r = $this->getSUT();
    $this->assertSame($expected,$r->get_single_column($key,$sorting_direction,$limit,$offset));
  }
  public function getSingleColumnData(){
    return array(
      array(array('b'=>4,'a'=>3),IRelationManager::ASC,null,null,array('z')),
      array(array('b'=>1,'a'=>1),IRelationManager::ASC,null,null,array('x','y','z')),
      array(array('b'=>1,'a'=>1),IRelationManager::DESC,null,null,array('z','y','x')),
      array(array('b'=>1,'a'=>1),IRelationManager::DESC,null,1,array('y','x')),
      array(array('b'=>1,'a'=>1),IRelationManager::DESC,1,1,array('y')),
      array(array('b'=>1,'a'=>1),IRelationManager::DESC,2,null,array('z','y')),
      array(array('b'=>51,'a'=>1),IRelationManager::DESC,null,null,array()),
    );
  }

}
?>
