<?php
interface IValidationException
{
  public function to_tree();
  //it should be developer-readable message, not the one for user
  public function getMessage();
}
?>
