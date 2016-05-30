<?php
class NullableValidator extends AbstractValidator implements IValidator
{
  private $inner;
  public function __construct(IValidator $inner){
    $this->inner = $inner;
  }
  public function get_error($data){
    if(null===$data){
      return null;
    }else{
      return $this->inner->get_error($data);
    }
  }
}
?>
