<?php
abstract class BasedEntitiesFactory extends MultiInstance implements IBasedEntitiesFactory
{
  abstract protected function get_type_id_to_function_name();
  public function get_by_type_id($type_id){
    $type_id_to_family = $this->get_type_id_to_function_name();
    $factory_function_name = Arrays::grab($type_id_to_family,$type_id);
    return $this->$factory_function_name();
  }
}
?>
