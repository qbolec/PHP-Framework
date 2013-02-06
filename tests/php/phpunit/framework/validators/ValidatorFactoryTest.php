<?php
class ValidatorFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new ValidatorFactory();
  }
  public function testInterface(){
    $this->assertInstanceOf('IGetInstance',$this->getSUT());
    $this->assertInstanceOf('IValidatorFactory',$this->getSUT());
    $this->assertInstanceOf('IValidator',$this->getSUT()->get_persistence_data(array('id'=>new IntFieldType())));
  }
}
?>
