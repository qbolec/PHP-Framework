<?php
class IsPCREValidator extends StringValidator
{
  public function get_error($data){
    $err = parent::get_error($data);
    if(null!==$err){
      return $err;
    }else if(false!==@preg_match($data,'')){
      return null;
    }else{
      return new WrongValueException($data); 
    }
  }
}
?>
