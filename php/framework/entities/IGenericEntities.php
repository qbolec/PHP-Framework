<?php
interface IGenericEntities extends IMultiGetByIds
{
  public function get_by_id($id);
  public function get_ids(array $entities);
}
?>
