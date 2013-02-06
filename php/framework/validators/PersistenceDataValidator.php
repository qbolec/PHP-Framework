<?php
class PersistenceDataValidator extends RecordValidator
{
  public function __construct(array $fields_description){
    $fields_validators = array();
    foreach($fields_description as $field_name => $type){
      $fields_validators[$field_name] = $type->get_validator();
    }
    parent::__construct($fields_validators);
  }
}
?>
