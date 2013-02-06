<?php
interface IValidationExceptionExplainer
{
  /**
   * @returns string
   */
  public function explain(IValidationException $e);
}
?>
