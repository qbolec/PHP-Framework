<?php
class PDORelationManagerTest extends FrameworkTestCase
{
  private function getSUT(){
    $fields_descriptor = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
      'c' => new StringFieldType(),
    ));
    $sharding = ShardingFactory::get_instance()->get_foreign_modulo('b');
    return new PDORelationManager($fields_descriptor,'relations','abc',$sharding);
  }
  public function testInterface(){
    $r = $this->getSUT();
    $this->assertInstanceOf('IRelationManager',$r);
  }
  /**
   * @expectedException IsMissingException
   */
  public function testGetSingleRowMiss(){
    $fields_descriptor = $this->getMock('IFieldsDescriptor');
    $sharding = $this->getMock('ISharding');
    $r = $this->getMock('PDORelationManager',array('get_all'),array($fields_descriptor,'relations','abc',$sharding));
    $r
      ->expects($this->once())
      ->method('get_all')
      ->will($this->returnValue(array()));
    $r->get_single_row(array('a'=>1,'b'=>13,'c'=>'h'));
  }
  public function testGetSingleRowDuplication(){
    $fields_descriptor = $this->getMock('IFieldsDescriptor');
    $sharding = $this->getMock('ISharding');
    $assertions = $this->getMock('IAssertions');
    $assertions
      ->expects($this->once())
      ->method('warn_if')
      ->with($this->equalTo(true));
    $r = $this->getMock('PDORelationManager',array('get_all','get_assertions'),array($fields_descriptor,'relations','abc',$sharding));
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
}
?>
