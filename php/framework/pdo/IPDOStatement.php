<?php
interface IPDOStatement
{
  /**
   * @throws PDOException
   * @throws LogicException if called at bad moment
   * @return array<map<column_name,value> > as with PDO::FETCH_ASSOC
   */
  public function fetchAll();
  /**
   * @throws PDOException
   * @throws LogicException if called at bad moment
   * @return map<column_name,value> as with PDO::FETCH_ASSOC
   */
  public function fetch();
  /**
   * @throws LogicException if called at bad moment
   * @return int
   */
  public function rowCount();
  /**
   * @return void (not bool as in original bindValue)
   * @throws PDOException 
   */
  public function execute();
  /**
   * @param $parameter_name in form :name
   * @param $value with the correct type
   * @param $pdo_data_type one of PDO::PARAM_* constants
   * @return void (not bool as in original bindValue)
   * @throws IValidationException if arguments are incorrect
   */
  public function bindValue($parameter_name,$value,$pdo_data_type);
  /**
   * @param $fields_description map<field_name,field_type>
   * @param $data map<field_name,normalized_value>
   */
  public function bindValues(array $fields_description,array $data);
}
?>
