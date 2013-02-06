<?php
class WrongValueException extends SimpleValidationException
{
  public function __construct($value){
    parent::__construct(JSON::encode($value) . ' is an incorrect value');
  }
}
?>
