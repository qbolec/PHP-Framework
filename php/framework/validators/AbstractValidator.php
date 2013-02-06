<?php
abstract class AbstractValidator implements IValidator
{
  public function is_valid($data){
    return null === $this->get_error($data);
  }
  public function must_match($data){
    $error = $this->get_error($data);
    if(null !== $error){
      throw $error;
    }
  }
  protected function compact_errors(array $errors){
    if(1<count($errors)){
      return new MultiValidationException($errors);
    }else if(1==count($errors)){
      return $errors[0];
    }else{
      return null;
    }
  }
  
  private $assertions = null;
  protected function halt_unless($b){
    if(!$b){
      if(null === $this->assertions){
        $this->assertions = Framework::get_instance()->get_assertions();
      }
      $this->assertions->halt_unless($b);
    }
  }
}
?>
