<?php
class StringFieldTypeTest extends PHPUnit_Framework_TestCase
{
  private function fieldType(){
    return new StringFieldType();
  }
  public function testInterface(){
    $f_t = $this->fieldType(); 
    $this->assertInstanceOf('IFieldType',$f_t);
    $this->assertInstanceOf('INormalizer',$f_t->get_normalizer());
    $this->assertInstanceOf('IValidator',$f_t->get_validator());
    $this->assertSame(PDO::PARAM_STR,$f_t->get_pdo_param_type('12'));//trochę dziwny test, ale nabierze sensu, jak będą też UNSIGNED itp
  }
  private function validator(){
    return $this->fieldType()->get_validator();
  }  
  private function normalizer(){
    return $this->fieldType()->get_normalizer();
  }
  public function testValidation(){
    $validator = $this->validator();
    $this->assertTrue($validator->is_valid('1'));
    $this->assertFalse($validator->is_valid(1));
  }
  public function testNormalizationSuccess(){
    $normalizer = $this->normalizer();
    $this->assertSame('1',$normalizer->normalize('1'));
  }
  /**
   * @expectedException CouldNotConvertException
   */
  public function testNormalizationFailure(){
    $normalizer = $this->normalizer();
    $normalizer->normalize(1);
  }
}
?>
