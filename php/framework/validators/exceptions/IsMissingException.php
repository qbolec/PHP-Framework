<?php
class IsMissingException extends SimpleValidationException
{
  public function __construct($field_name){
    parent::__construct($field_name . ' is missing');
  }
}
?>
