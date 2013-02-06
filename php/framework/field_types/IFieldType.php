<?php
interface IFieldType
{
  /**
   *  accepts types larger than the field type, as long as the value can be interpreted as the field type.
   *  @example PDO always returns strings, even for columns which are of type INT UNSIGNED
   *  this can be used by PersistenceManager to convert PDO result to expected field type
   *  @return INormalizer
   */
  public function get_normalizer();
  /**
   * checks if the PHP variable has _exactly_ the type the field has.
   * this can be used by Entity to verify data in constructor
   * @return IValidator
   */
  public function get_validator();
  /**
   * @return one of PDO::PARAM_* to be used with pdo_bind_param/value
   */
  public function get_pdo_param_type($value);
  /**
   * @return one of SORT_* flags used by PHP sort functions
   */
  public function get_sort_type();
}
?>
