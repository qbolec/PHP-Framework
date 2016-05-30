<?php
abstract class AbstractRelation extends Singleton implements IRelation
{
  private $manager;
  public function __construct(IRelationManager $manager){
    $this->manager = $manager;
  }
  protected function get_manager(){
    return $this->manager;
  }
  protected function get_column_values(array $key, $column_name, $sort_direction=IRelationManager::DESC,$limit=null,$offset=null){
    return Arrays::pluck($this->get_manager()->get_all($key,array(
      $column_name=>$sort_direction,
    ),$limit,$offset),$column_name);
  }
  protected function get_unique_column_values(array $key, $column_name, $sort_direction=IRelationManager::DESC){
    return Arrays::unique_values($this->get_column_values($key,$column_name,$sort_direction));
  }
}
?>
