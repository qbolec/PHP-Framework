<?php
abstract class AbstractSimplePersistentEntities extends AbstractEntities
{
  protected $persistence_manager;
  public function __construct(IPersistenceManager $persistence_manager){
    $this->persistence_manager = $persistence_manager;
  }
  public function get_by_id($id){
    $data = $this->persistence_manager->get_by_id($id);
    return $this->from_data($data);
  }
  public function multi_get_by_ids(array $ids){
    $datas = $this->persistence_manager->multi_get_by_ids($ids);
    $entities = array();
    foreach($datas as $id => $data){
      $entities[$id] = $this->from_data($data);
    }
    return $entities;
  }
  abstract protected function from_data(array $data);
  protected function insert(array $data){
    //ignorujemy kolizje dość świadomie:
    //od inserta wcale nie oczekujemy wykrywania kolizji,
    //bo psułoby to np. layered persistent manager : do insertując do cacheu może się przytrafić kolizja
    $this->persistence_manager->insert($data);
    return $this->from_data($data);
  }
  protected function insert_and_assign_id(array $data){
    $id = $this->persistence_manager->insert_and_assign_id($data);
    $data['id'] = $id;
    return $this->from_data($data);
  }
}
?>
