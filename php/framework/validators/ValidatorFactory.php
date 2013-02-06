<?php
class ValidatorFactory extends MultiInstance implements IValidatorFactory
{
  public function get_persistence_data(array $fields_description){
    return new PersistenceDataValidator($fields_description);
  }
}
?>
