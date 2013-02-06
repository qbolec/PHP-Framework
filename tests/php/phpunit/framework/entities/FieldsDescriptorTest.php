<?php
class FieldsDescriptorTest extends FrameworkTestCase
{
  public function testInterface(){
    $fd = new FieldsDescriptor(array());
    $this->assertInstanceOf('IFieldsDescriptor',$fd);
  }
  public function testGetDescription(){
    $d = array(
      'id' => new IntFieldType(),
    );
    $fd = new FieldsDescriptor($d);
    $this->assertSame($d,$fd->get_description());
  }
  public function testGetValidator(){
    $d = array(
      'id' => new IntFieldType(),
    );
    $fd = new FieldsDescriptor($d);
    $v=$fd->get_validator();
    $this->assertInstanceOf('IValidator',$v);
    $this->assertSame(false,$v->is_valid(13));
    $this->assertSame(false,$v->is_valid(array('id'=>13,'x'=>'y')));
    $this->assertSame(true,$v->is_valid(array('id'=>13)));
  }
}
?>
