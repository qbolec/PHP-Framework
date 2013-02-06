<?php
class FixedIntMapValidator extends FixedKeysValidator
{
  public function __construct(array $field_validators){
    foreach($field_validators as $field_name => $validator){
      $this->halt_unless(is_int($field_name));
    }
    parent::__construct($field_validators);
  }
  protected function get_key_validator(){
    return new IntValidator();
  }
}
?>
