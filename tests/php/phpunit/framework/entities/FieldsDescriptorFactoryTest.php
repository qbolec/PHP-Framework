<?php
class FieldsDescriptorFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new FieldsDescriptorFactory();
  }
  public function testInterface(){
    $fdf = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$fdf);
    $this->assertInstanceOf('IFieldsDescriptorFactory',$fdf);

    $a = $fdf->get_from_array(array());
    $this->assertInstanceOf('IFieldsDescriptor',$a);
    $this->assertInstanceOf('IFieldsDescriptor',$fdf->get_merged($a,$a));
  }
  public function testGetFromArray(){
    $a = array(
      'id' => new IntFieldType(),
    );
    $afd = $this->getSUT()->get_from_array($a);
    $this->assertSame($a,$afd->get_description()); 
  }
  public function testGetMerged(){
    $a = array(
      'id' => new IntFieldType(),
    );
    $afd = $this->getSUT()->get_from_array($a);
    $b = array(
      'person_id' => new StringFieldType(),
    );
    $bfd = $this->getSUT()->get_from_array($b);
    $abfd = $this->getSUT()->get_merged($afd,$bfd);
    $this->assertEquals(array_merge($a,$b),$abfd->get_description()); 
  }


}
?>
