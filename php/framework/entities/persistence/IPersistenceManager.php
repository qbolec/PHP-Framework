<?php
interface IPersistenceManager extends IGetFieldsDescriptor
{
  /**
   * @return array<string key,normalized value>
   */
  public function get_by_id($id);
  /**
   * @return bool was anything actually deleted?
   */
  public function delete_by_id($id);
  /**
   * @param $data array<string key,normalized value> without id
   * @return int id
   */
  public function insert_and_assign_id(array $data);
  /**
   * @param $data array<string key,normalized value> with id
   * @return bool true if there was no id duplication error
   */
  public function insert(array $data);

  /**
   * @param @current_data array<string key,normalized value> with id
   * @param @original_data array<string key,normalized value> not every key must be listed
   */
  public function save(array $current_data,array $original_data);

  /**
   * @param ids array<id>
   * @return map<id,Entity> missing Entities are missing in the returned map
   */
  public function multi_get_by_ids(array $ids);
}
?>
