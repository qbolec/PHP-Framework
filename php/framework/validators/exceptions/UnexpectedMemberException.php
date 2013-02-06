<?php
class UnexpectedMemberException extends SimpleValidationException
{
  public function __construct($member){
    parent::__construct($member . ' was unexpected');
  }
}
?>
