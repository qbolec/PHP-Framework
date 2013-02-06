<?php
class SimpleValidationException extends LogicException implements IValidationException
{
  public function to_tree(){
    return array('errors'=>array($this));
  }
}
?>
