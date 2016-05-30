<?php
abstract class AbstractGenericEntities extends Singleton implements IGenericEntities
{
  //tę funkcję należy nadpisać jeśli się chce coś zoptymalizować
  //@see AbstractSimplePersistentEntities::multi_get_by_ids
  public function multi_get_by_ids(array $ids){
    $entities = array();
    foreach($ids as $id){
      try{
        $entities[$id] = $this->get_by_id($id);
      }catch(NoSuchEntityException $e){
      }
    }
    return $entities;
  }
  public function get_ids(array $entities){
    return array_map(function($entity){
      return $entity->get_id();
    },$entities);
  }
}
?>
