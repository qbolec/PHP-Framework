<?php
class PCREValidator extends StringValidator
{
  private $pcre;
  public function __construct($pcre){
    $this->pcre = $pcre;
  }
  public function get_error($data){
    $err = parent::get_error($data);
    if(null!==$err){
      return $err;
    }else if(preg_match($this->pcre,$data)){
      return null;
    }else{
      return new WrongValueException($data); 
    }
  }
}
?>
