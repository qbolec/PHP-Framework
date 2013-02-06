<?php
class RelationManagerFactory extends AbstractTableManagerFactory implements IRelationManagerFactory
{
  protected function get_type_to_factory(){
    return array(
      'pdo' => 'PDORelationManagerFactory',
      'cached' => 'CachedRelationManagerFactory',
    );
  }
  protected function get_path($name){
    return "relations/$name";
  }
  public function get_array(IFieldsDescriptor $fields_descriptor,array $data,array $unique_keys){
    return new ArrayRelationManager($fields_descriptor,$data,$unique_keys);
  }
}
?>
