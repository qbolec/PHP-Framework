<?php
class CouldNotConvertException extends SimpleValidationException
{
  public function __construct($whatever){
    parent::__construct("Could not convert " . JSON::encode($whatever));
  }
}
?>
