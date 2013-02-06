<?php
class NullableFieldTypeTest extends FrameworkTestCase
{
  private function getSUT($inner){
    return new NullableFieldType($inner);
  }
  public function testInterface(){
    $inner = $this->getMock('IFieldType');
    $field_type = $this->getSUT($inner);
    $this->assertInstanceOf('IFieldType',$field_type);
  }
  public function testGetValidator(){
    $inner = $this->getMock('IFieldType');
    $inner
      ->expects($this->once())
      ->method('get_validator')
      ->will($this->returnValue(new IntValidator()));
    $field_type = $this->getSUT($inner);
    $this->assertInstanceOf('IValidator',$field_type->get_validator());
  }
  public function testGetNormalizer(){
    $inner = $this->getMock('IFieldType');
    $inner
      ->expects($this->once())
      ->method('get_normalizer')
      ->will($this->returnValue(new IntLikeValidator()));
    $field_type = $this->getSUT($inner);
    $this->assertInstanceOf('INormalizer',$field_type->get_normalizer());
  }
  public function testNull(){
    $inner = $this->getMock('IFieldType');
    $inner
      ->expects($this->never())
      ->method('get_pdo_param_type');
    $field_type = $this->getSUT($inner);
    $this->assertSame(PDO::PARAM_NULL,$field_type->get_pdo_param_type(null));
  }
  public function testNotNull(){
    $value = 42;
    $t = PDO::PARAM_INT;
    $inner = $this->getMock('IFieldType');
    $inner
      ->expects($this->once())
      ->method('get_pdo_param_type')
      ->with($this->equalTo($value))
      ->will($this->returnValue($t));
    $field_type = $this->getSUT($inner);
    $this->assertSame($t,$field_type->get_pdo_param_type($value));
  }
  public function testSortType(){
    $t = 42;
    $inner = $this->getMock('IFieldType');
    $inner
      ->expects($this->once())
      ->method('get_sort_type')
      ->will($this->returnValue($t));
    $field_type = $this->getSUT($inner);
    $this->assertSame($t,$field_type->get_sort_type());
  }
}
?>
