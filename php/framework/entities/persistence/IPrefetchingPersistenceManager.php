<?php
interface IPrefetchingPersistenceManager extends IPersistenceManager
{
  /**
   * @param id of entity to prefetch
   */
  public function prefetch_by_id($id);
}
?>
