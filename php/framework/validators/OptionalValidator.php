<?php
class OptionalValidator extends AbstractValidator implements IValidator
{
  private $inner;
  public function __construct(IValidator $inner){
    $this->inner = $inner;
  }
  public function get_error($data){
    return $this->inner->get_error($data);
  }
}
?>
