<?php
class PDOStatementEx implements IPDOStatement
{
  private $statement;
  private $executed;
  private $bound;
  public function __construct(PDOStatement $statement){
    $this->statement = $statement;
    $this->executed = false;
    $this->bound = array();
  }
  private function halt_unless_executed(){
    Framework::get_instance()->get_assertions()->halt_unless($this->executed);
  }
  public function fetchAll(){
    $this->halt_unless_executed();
    return $this->statement->fetchAll(PDO::FETCH_ASSOC);
  }
  public function fetch(){
    $this->halt_unless_executed();
    return $this->statement->fetch(PDO::FETCH_ASSOC);
  }
  public function rowCount(){
    $this->halt_unless_executed();
    return $this->statement->rowCount();
  }
  public function execute(){
    Framework::get_instance()->get_logger()->log($this->statement->queryString);
    $this->statement->execute();
    $this->executed = true;
    $this->bound = array();
  }
  public function bindValue($parameter_name,$value,$pdo_data_type){
    $type_to_check = array(
      PDO::PARAM_INT => 'is_int',
      PDO::PARAM_STR => 'is_string',
      PDO::PARAM_BOOL => 'is_bool',
      PDO::PARAM_NULL => 'is_null',
      FloatFieldType::TYPE => 'is_float',
    );
    $check = Arrays::grab($type_to_check,$pdo_data_type);
    if(!($check($value))){
      throw new CouldNotConvertException($value);
    }
    if(!preg_match('/:\w+/',$parameter_name)){
      throw new WrongValueException($parameter_name);
    }
    if(false===strpos($this->statement->queryString,$parameter_name)){
      throw new UnexpectedMemberException($parameter_name);
    }
    if(array_key_exists($parameter_name,$this->bound)){
      throw new UnexpectedMemberException($parameter_name);
    }
    $this->bound[$parameter_name] = true;
    //PHP PDO does not define PDO::PARAM_FLOAT
    $this->statement->bindValue($parameter_name,$value,$pdo_data_type===FloatFieldType::TYPE?PDO::PARAM_STR:$pdo_data_type);
  }
  public function bindValues(array $fields_description,array $data){
    foreach($data as $field_name => $value){
      $field_type = $fields_description[$field_name];
      $this->bindValue(':'.$field_name,$value,$field_type->get_pdo_param_type($value));
    }
  }
}
?>
