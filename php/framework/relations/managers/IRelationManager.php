<?php
interface IRelationManager extends IGetFieldsDescriptor
{
  const ASC = 0;
  const DESC = 1;
  const ASC_NULL_LAST = 2;
  const DESC_NULL_LAST = 3;
  const ASC_NULL_FIRST = 4;
  const DESC_NULL_FIRST = 5;
  /**
   * @return int number of entities matching key
   */
  public function get_count(array $key);
  /**
   * @param $key map<field_name,value> pattern
   * @param $order_by map<field_name,ASC/DESC>
   * @param $limit int
   * @param $offset int
   * @return array<map<field_name,value> > ordered and sliced. Only other columns are reported
   */
  public function get_all(array $key=array(),array $order_by=array(),$limit=null,$offset=null);
   /**
   * @param $key map<field_name,value> pattern. Must be all but one column.
   * @param $sort_direction ASC/DESC
   * @param $limit int
   * @param $offset int
   * @return array<value > ordered and sliced. Only the missing column is reported
   */
  public function get_single_column(array $key, $sort_direction=self::DESC,$limit=null,$offset=null);
  /**
   * @param $key map<field_name,value> pattern. Must be unique.
   * @return map<field_name,value>. Only other columns are reported.
   * @throws IsMissingException if there is no such row
   * @throws LogicException if there are more than one row
   */
  public function get_single_row(array $key);
  /**
   * This is a batched version of get_single_row.
   * @param $keys array<map<field_name,value> > @see get_single_row. All keys should have same columns.
   * @return array<map<field_name,value> > . Only other columns are returned for each row. If row is missing, result contains null.
   * @throws LogicException if there are more than one row per key, or if two different keys contain different columns.
   */
  public function get_multiple_rows(array $keys);
  /**
   * @return bool true if there was no duplication
   */
  public function insert(array $key);
  /**
   * @return int number of rows deleted
   */
  public function delete(array $key);
}
?>
