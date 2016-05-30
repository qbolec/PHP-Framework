<?php
class ShardingFactory extends AbstractConfigurableConnectionsFactory implements IShardingFactory
{
  public function get_none(){
    return new NoSharding();
  }
  public function get_foreign_modulo($field_name){
    return new ForeignSharding($field_name,new ModuloSelectSharding());
  }
  public function get_foreign_modulo_consistent($field_name,$ring_bits){
    $assertions = Framework::get_instance()->get_assertions();
    return new ForeignSharding($field_name,new ConsistentSelectSharding($ring_bits,$assertions),new ModuloSelectSharding());
  }
  public function get_foreign($field_name,ISelectSharding $select,ISelectSharding $insert){
    return new ForeignSharding($field_name,$select,$insert);
  }
  private function get_consistent($ring_bits){
    $assertions = Framework::get_instance()->get_assertions();
    return new RandomSharding(new ConsistentSelectSharding($ring_bits,$assertions));
  }
  private function get_string($inner_name){
    $inner = $this->from_config_name($inner_name);
    return new StringSharding($inner);
  }
  public function from_config_name($name){
    $path = "shardings/$name";
    return $this->get_connection_for_config_path($path);
  }
  protected function spawn(array $info){
    $type = $info['type'];
    $config = $info['config'];
    switch($type){
    case 'none':
      return $this->get_none();
    case 'consistent':
      return $this->get_consistent($config);
    case 'string':
      return $this->get_string($config);
    case 'modulo':
      return $this->get_foreign_modulo(Arrays::grab($config,'field_name'));
    case 'modulo-consistent':
      return $this->get_foreign_modulo_consistent(Arrays::grab($config,'field_name'),Arrays::grab($config,'bits'));
    case 'foreign':
      return $this->get_foreign(Arrays::grab($config,'field_name'),$this->from_config_name(Arrays::grab($config,'select')),$this->from_config_name(Arrays::grab($config,'insert')));
    default:
      //@TODO
      throw new LogicException('not implemented yet');
    }
  }
}
?>
