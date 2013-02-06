<?php
class ValidationExceptionExplainerFactory extends MultiInstance implements IValidationExceptionExplainerFactory
{
  public function get_dev(){
    return DevValidationExceptionExplainer::get_instance();
  }
  public function get_json(){
    return JSONValidationExceptionExplainer::get_instance();
  }
}
?>
