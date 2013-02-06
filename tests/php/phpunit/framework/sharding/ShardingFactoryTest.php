<?php
class ShardingFactoryTest extends FrameworkTestCase
{
  public function getSUT(){
    return new ShardingFactory();
  }
  public function testInterface(){
    $sharding_factory = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$sharding_factory);
    $this->assertInstanceOf('IShardingFactory',$sharding_factory);
    $this->assertInstanceOf('ISharding',$sharding_factory->get_none());
    $this->assertInstanceOf('ISharding',$sharding_factory->get_foreign_modulo('a'));
  }
  public function testGetNone(){
    $sharding_factory = $this->getSUT();
    $sharding = $sharding_factory->get_none();
    $this->assertInstanceOf('ISharding',$sharding);
    $this->assertSame(0,$sharding->get_shard_id_from_data_without_id(1,array('bla'=>13)));
  }
  public function testGetForeignModulo(){
    $field_name = 'bla';
    $sharding_factory = $this->getSUT();
    $sharding = $sharding_factory->get_foreign_modulo($field_name);
    $this->assertInstanceOf('ISharding',$sharding);
    $this->assertSame(2,$sharding->get_shard_id_from_data_without_id(11,array($field_name=>13)));
  }
  public function testFromConfigName(){
    $shardings = array(
      'none'=> array(
        'type'=>'none',
        'config'=>null,
      ),
      'consistent'=>array(
        'type'=>'consistent',
        'config'=>31,
      ),
      'string-consistent'=>array(
        'type'=>'string',
        'config'=>'consistent',
      ),
      'stat_counters_log'=>array(
        'type'=>'modulo',
        'config'=>array(
          'field_name'=>'stat_counter_id',
        ),
      ),
    );
    $this->setConfig(array(
      'shardings'=>$shardings,
    ));
    $sharding_factory=$this->getSUT();
    foreach(array_keys($shardings) as $sharding_name){
      $this->assertInstanceOf('ISharding',$sharding_factory->from_config_name($sharding_name));
    }
  } 
  /**
   * @expectedException LogicException
   */
  public function testFromBadConfigName(){
    $shardings = array(
      'none'=> array(
        'type'=>'atlantis',
        'config'=>null,
      ),
    );
    $this->setConfig(array(
      'shardings'=>$shardings,
    ));
    $sharding_factory=$this->getSUT();
    $sharding_factory->from_config_name('none');
  } 
}
?>
