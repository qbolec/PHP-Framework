<?php
abstract class AbstractRelationImp extends AbstractRelation
{
  public function get_manager(){
    return parent::get_manager();
  }
}
class AbstractRelationTest extends FrameworkTestCase
{
  public function testInterface(){
    $rm = $this->getMock('IRelationManager');
    $relation = $this->getMockForAbstractClass('AbstractRelationImp',array($rm));
    $this->assertInstanceOf('IRelation',$relation);
    $this->assertSame($rm,$relation->get_manager());
  }
}
?>
