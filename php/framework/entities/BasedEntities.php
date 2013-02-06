<?php
abstract class BasedEntities extends Singleton implements IGenericEntities
{
  abstract protected function get_family_factory();
  public function get_by_id($id){
    return $this->get_family_by_id($id)->get_by_id($id);
  }
  private function get_family_by_id($id){
    $family_factory = $this->get_family_factory(); 
    $base_entity = $family_factory->get_base()->get_by_id($id);
    return $family_factory->get_by_type_id($base_entity->get_type_id());
  }
  public function multi_get_by_ids(array $ids){
    $base = $this->get_family_factory()->get_base();
    $base_pm = $base->get_persistence_manager();
    foreach($ids as $id){
      $base_pm->prefetch_by_id($id);
    }
    $families = array();
    foreach($ids as $id){
      try{
        $family = $this->get_family_by_id($id);
        $family->get_persistence_manager()->prefetch_by_id($id);
        $families[$id] = $family;
      }catch(NoSuchEntity $e){
      }
    }
    $result = array();
    foreach($families as $id => $family){
      try{
        $result[$id] = $family->get_by_id($id);
      }catch(NoSuchEntity $e){
      }
    }
    return $result;
  }
}
?>
