<?php
abstract class AbstractEditablePersistentEntities extends AbstractSimplePersistentEntities implements IEditableEntities
{
  public function save(array $current_data,array $original_data){
    return $this->persistence_manager->save($current_data,$original_data);
  }
  public function get_fresh_data($id){
    return $this->persistence_manager->get_by_id($id);
  }
}
?>
