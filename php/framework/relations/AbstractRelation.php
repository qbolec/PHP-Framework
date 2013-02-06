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
}
?>
