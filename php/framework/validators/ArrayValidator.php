<?php
class ArrayValidator extends MapValidator
{
  public function __construct(IValidator $member_validator){
    parent::__construct(new IntValidator(),$member_validator);
  }
  public function get_error($data){
    $error = parent::get_error($data);
    $errors = array();
    if(null!==$error){
      $errors[] = $error;
    }
    if(is_array($data)){
      for($i=count($data);$i--;){
        if(!array_key_exists($i,$data)){
          $errors[] = new IsMissingException($i);
        }
      }
    }
    return $this->compact_errors($errors);
  }
}
?>
