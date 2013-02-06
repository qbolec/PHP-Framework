<?php
interface IValidator
{
  public function is_valid($data);
  /**
   * @return IValidationException
   */
  public function get_error($data);
  /**
   * @throws IValidationException
   */
  public function must_match($data);
}
?>
