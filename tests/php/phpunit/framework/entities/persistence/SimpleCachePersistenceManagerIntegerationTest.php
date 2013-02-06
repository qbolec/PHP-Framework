<?php
class SimpleCachePersistenceManagerIntegerationTest extends FrameworkTestCase
{
  public function testInsertSaveDeleteGetAndMultiGet(){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(array(
        'logging'=>array(
          'rules'=>array(
          ),
        ),
        'caches'=>array(
          'test-cache-name'=>array(
            'type'=>'layered',
            'config'=>array(
              'near'=>'array-a',
              'far'=>'local',
            ),
          ),
          'array-a'=>array(
            'type'=>'array',
            'config'=>null,
          ),
          'local'=>array(
            'type'=>'memcache',
            'config'=>array(
              'cluster'=>'local',
              'ttl'=>10,
            ),
          ),
        ),
        'memcaches'=>array(
          'clusters'=>array(
            'local'=>array(
              'servers'=>array(
                array(
                  'host'=>'localhost',
                  'port'=>11211,
                ),
              ),
            ),
          ),
        ),
      )));

    $this->set_global_mock('Config',$config);
    
    $user[42]=array(
      'id'=>42,
      'person_id'=>'abc',
    );
    $old_user=array(
      'id'=>43,
      'person_id'=>'def',
    );
    $user[43]=$old_user;
    $pm = new SimpleCachePersistenceManager($this->getUserlikeFieldsDescriptor(),Framework::get_instance()->get_cache_factory()->get_cache('test-cache-name'),'test-');
    $pm
      ->insert($user[42]);
    $pm
      ->insert($user[43]);
    $user[43]['person_id']='ghi';
    $pm
      ->save($user[43],$old_user);
    $pm
      ->delete_by_id(42);
    $this->assertSame(array(43=>$user[43]),$pm->multi_get_by_ids(array(42,43)));
  
  }
}
?>
