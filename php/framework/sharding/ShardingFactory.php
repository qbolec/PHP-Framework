<?php
class ShardingFactory extends AbstractConfigurableConnectionsFactory implements IShardingFactory
{
  public function get_none(){
    return new NoSharding();
  }
  public function get_foreign_modulo($field_name){
    return new ForeignSharding($field_name,new ModuloSelectSharding());
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
    default:
      //@TODO
      throw new LogicException('not implemented yet');
    }
  }
}
?>
