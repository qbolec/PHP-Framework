<?php
abstract class AbstractExtendablePersistentEntities extends AbstractEditablePersistentEntities implements IExtendableEntities
{
  public function get_persistence_manager(){
    return $this->persistence_manager;
  }
}
?>
