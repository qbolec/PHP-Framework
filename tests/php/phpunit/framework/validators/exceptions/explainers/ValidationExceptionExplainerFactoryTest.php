<?php
class ValidationExceptionExplainerFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $f = ValidationExceptionExplainerFactory::get_instance();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IValidationExceptionExplainerFactory',$f);
    $this->assertInstanceOf('IValidationExceptionExplainer',$f->get_dev());
    $this->assertInstanceOf('IValidationExceptionExplainer',$f->get_json());
  }
}
?>
