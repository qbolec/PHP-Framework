<?php
class AbstractExtendedPersistentEntitiesTest extends FrameworkTestCase
{
  public function testInterface(){
    $pm = $this->getMock('IPersistenceManager');
    $a = $this->getMockForAbstractClass('AbstractExtendedPersistentEntities',array($pm));
    $this->assertInstanceOf('IEntities',$a);
    $this->assertInstanceOf('ISharedPersistence',$a);
  }
  public function testGetFieldsDescriptor(){
    $pm = $this->getMock('IPersistenceManager');
    $a = $this->getMockForAbstractClass('AbstractExtendedPersistentEntities',array($pm));
    $bfd = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'id'=>new IntFieldType(),
      'b' =>new StringFieldType(),
    ));
    $efd = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'id'=>new IntFieldType(),
      'e' =>new StringFieldType(),
    ));
    $base_family = $this->getMock('IEntities');
    $base_family
      ->expects($this->once())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($bfd));
    $a
      ->expects($this->once())
      ->method('get_base_family')
      ->will($this->returnValue($base_family));
    $a
      ->expects($this->once())
      ->method('get_extension_fields_descriptor')
      ->will($this->returnValue($efd));
    $this->assertEquals(array('id'=>new IntFieldType(),'b'=>new StringFieldType(),'e'=>new StringFieldType()),$a->get_fields_descriptor()->get_description());
  }
}
?>
