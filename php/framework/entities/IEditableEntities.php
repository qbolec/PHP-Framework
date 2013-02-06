<?php
interface IEditableEntities extends IEntities
{
  /**
   * @friend IEditableEntity
   */
  public function save(array $current_data,array $original_data); 

  /**
   * @returns map<string,?> field values, as for __construct
   * @friend IEditableEntity
   */
  public function get_fresh_data($id);
}
?>
