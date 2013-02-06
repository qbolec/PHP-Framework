<?php
interface IEditableEntity extends IEntity
{
  public function begin();
  public function commit(ILock $lock=null);
}
?>
