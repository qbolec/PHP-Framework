<?php
class CacheVersioningAux extends CacheVersioning
{
  public function get_keys_names_affected_by(array $eqs){
    return parent::get_keys_names_affected_by($eqs);
  } 
  public function get_keys_names_affecting(array $eqs){
    return parent::get_keys_names_affecting($eqs);
  }
}
class CacheVersioningTest extends FrameworkTestCase
{
  public function getSUT(array $keys){
    return new CacheVersioning(new PrefetchingCacheWrapper(new ArrayCache()),'test',$keys);
  }
  public function testInterface(){
    $cv = $this->getSUT(array());
    $this->assertInstanceOf('ICacheVersioning',$cv);
  }
  private function setTimingFramework(){
    $framework = $this->getMock('Framework',array('get_time'));
    $this->set_global_mock('Framework',$framework);
    return $framework;
  }
  public function getCorrectAffections(){
    return array(
      array( array(), array('***'),array('???','??*','?*?','?**','*??','*?*','**?','***'),  ),
      array( array('x'=>1), array('1**','?**'),array('1??','1?*','1*?','1**','*??','*?*','**?','***'),  ),
      array( array('x'=>1,'y'=>0), array('10*','?0*','1?*','??*'),array('10?','10*','1*?','1**','*0?','*0*','**?','***'),  ),
    );
  }
  private function decode_string($string){
    $dim = 'xyz';
    $result = array();
    for($i=0;$i<3;++$i){
      $c = $string[$i];
      if($c!='*'){
        $result[] = $dim[$i] . '=' . $c; 
      }
    }
    return 'test/version?' . implode('&',$result);
  }
  private function decode(array $encoded){
    return array_map(array($this,'decode_string'),$encoded);
  }
  /**
   * @dataProvider getCorrectAffections
   */
  public function testCorrectAffections($me,$affecting_me,$affected_by_me){
    $cv = new CacheVersioningAux(new PrefetchingCacheWrapper(new ArrayCache()),'test' ,array('x','y','z'));
    $affecting_me = $this->decode($affecting_me);
    $affected_by_me = $this->decode($affected_by_me);
    $this->assertIsPermutationOf($affecting_me,$cv->get_keys_names_affecting($me));
    $this->assertIsPermutationOf($affected_by_me,$cv->get_keys_names_affected_by($me));
  }
  public function testGetVersionInitialIsMonotoneNumber(){
    $fake_time = 10;
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->exactly(2))
      ->method('get_time')
      ->will($this->returnCallback(function()use(&$fake_time){return $fake_time;}));

    $key = array('id'=>7,'parent_id'=>13);
    $cv = $this->getSUT(array_keys($key));
    $v1 = $cv->get_version($key);
    //$this->assertInternalType('int',$v1);
    
    //new instance:
    ++$fake_time;
    $cv = $this->getSUT(array_keys($key));
    $v2 = $cv->get_version($key);
    //$this->assertInternalType('int',$v2);

    $this->assertTrue($v1<$v2);
  }
  public function testGetVersionIsPermanent(){
    $fake_time = 10;
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->once())
      ->method('get_time')
      ->will($this->returnCallback(function()use(&$fake_time){return $fake_time;}));

    $key = array('id'=>7,'parent_id'=>13);
    $cv = $this->getSUT(array_keys($key));
    $v1 = $cv->get_version($key);
    //$this->assertInternalType('int',$v1);
    
    //same instance:
    ++$fake_time;
    $v2 = $cv->get_version($key);
    //$this->assertInternalType('int',$v2);

    $this->assertSame($v1,$v2);
  }
  public function testGetVersionsIsConsistent(){
    $fake_time = 10;
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->once())
      ->method('get_time')
      ->will($this->returnCallback(function()use(&$fake_time){return $fake_time;}));

    $key = array('id'=>7,'parent_id'=>13);
    $cv = $this->getSUT(array_keys($key));
    $v1 = $cv->get_version($key);
    //$this->assertInternalType('int',$v1);
    
    //same instance:
    ++$fake_time;
    list($v2,$v3) = $cv->get_versions(array($key,$key));
    //$this->assertInternalType('int',$v2);

    $this->assertSame($v1,$v2);
    $this->assertSame($v2,$v3);
  }
  /**
   * @dataProvider getNotAffecting
   */
  public function testInvalidationDoesNotSpoil($updated){
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->once())
      ->method('get_time')
      ->will($this->returnValue(10));

    $key = array('id'=>7,'parent_id'=>13);
    $cv = $this->getSUT(array_keys($updated));
    $v1 = $cv->get_version($key);
    //$this->assertInternalType('int',$v1);
    
    $cv->invalidate($updated);
    $v2 = $cv->get_version($key);
    //$this->assertInternalType('int',$v2);

    $this->assertSame($v1,$v2);
  }
  public function getNotAffecting(){
    return array(
      array(array('id'=>8,'parent_id'=>13,'extra'=>1)),
      array(array('parent_id'=>8,'id'=>7,'extra'=>1)),
      array(array('parent_id'=>7,'id'=>13)),
      array(array('id'=>13,'parent_id'=>7)),
      array(array('id'=>8,'parent_id'=>13)),
      array(array('parent_id'=>8,'id'=>7)),
      array(array('parent_id'=>8)),
      array(array('id'=>8)),
    );
  }

  /**
   * @dataProvider getAffecting
   */
  public function testInvalidationIsMonotoneNumber($updated){
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->once())
      ->method('get_time')
      ->will($this->returnValue(10));

    $key = array('id'=>7,'parent_id'=>13);
    $cv = $this->getSUT(array_keys($updated));
    $v1 = $cv->get_version($key);
    //$this->assertInternalType('int',$v1);
    
    $cv->invalidate($updated);
    $v2 = $cv->get_version($key);
    //$this->assertInternalType('int',$v2);

    $this->assertTrue($v1<$v2);
  }
  public function getAffecting(){
    return array(
      array(array('id'=>7,'parent_id'=>13)),
      array(array('parent_id'=>13,'id'=>7)),
      array(array('id'=>7,'parent_id'=>13,'extra'=>13)),
      array(array('id'=>7,'extra'=>13)),
      array(array('parent_id'=>13,'extra'=>13)),
      array(array('id'=>7)),
      array(array('parent_id'=>13)),
      array(array()),
      array(array('extra'=>13)),
    );
  }

  /**
   * @dataProvider getAffected
   */
  public function testInvalidatesEverySubset($key){
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->once())
      ->method('get_time')
      ->will($this->returnValue(10));

    $updated = array('id'=>7,'parent_id'=>13);
    $cv = $this->getSUT(array_keys($updated));
    $v1 = $cv->get_version($key);
//    $this->assertInternalType('int',$v1);
    
    $cv->invalidate($updated);
    $v2 = $cv->get_version($key);
//    $this->assertInternalType('int',$v2);

    $this->assertTrue($v1<$v2);
  }
  public function getAffected(){
    return array(
      array(array()),
      array(array('parent_id'=>13)),
      array(array('id'=>7)),
      array(array('id'=>7,'parent_id'=>13)),
      array(array('id'=>7,'parent_id'=>13,'extra'=>13)),
      array(array('parent_id'=>13,'id'=>7)),
    );
  }
  public function testRaceConditionGetCount(){
    $fake_time = 10;
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->atLeastOnce())
      ->method('get_time')
      ->will($this->returnCallback(function()use(&$fake_time){return $fake_time;}));
    
    $key = array('a'=>13);

    $cache = new ArrayCache();
    $mock_cache = $this->getMock('PrefetchingCacheWrapper',array('add'),array($cache));
    $cache2 = new PrefetchingCacheWrapper($cache);
    $wrapped = new CacheVersioning($mock_cache,'test',array('a'));
    $wrapped2 = new CacheVersioning($cache2,'test',array('a'));

    $rc_value = null;
    $mock_cache
      ->expects($this->atLeastOnce())
      ->method('add')
      ->will($this->returnCallback(function ($k,$v)use($key,$wrapped2,$cache,&$fake_time,&$rc_value){
        $fake_time--;
        $rc_value = $wrapped2->get_version($key);
        $fake_time++;
        return $cache->add($k,$v);
      }));
    
    $value=$wrapped->get_version($key);
    $this->assertSame($rc_value,$value);
    $this->assertSame($value,$wrapped->get_version($key));
    $this->assertSame($value,$wrapped2->get_version($key));
  }
  /**
   * @dataProvider getIncomplete
   */
  public function testInvalidateDoesNotRequireFullKnowlege($incomplete){
    $cv = $this->getSUT(array('a','b'));
    $cv->invalidate($incomplete);
  }
  public function getIncomplete(){
    return array(
      array(array('a'=>1,'c'=>2)),
      array(array('a'=>1)),
    );
  }

  public function getFull3D(){
    $f = array('x','y','z');
    $tests = array();
    for($m[0]=0;$m[0]<8;++$m[0]){
      for($v[0]=0;$v[0]<8;++$v[0])if(($m[0]&$v[0]) == $v[0]){
        for($m[1]=0;$m[1]<8;++$m[1]){
          for($v[1]=0;$v[1]<8;++$v[1])if(($m[1]&$v[1]) == $v[1]){
            $e = array(array(),array());
            for($i=0;$i<3;++$i){
              for($x=0;$x<2;++$x){
                if($m[$x] & (1<<$i)){
                  $e[$x][$f[$i]] = (($v[$x]>>$i)&1);
                }
              }
            }
            $cross = !(($m[0] & $m[1]) & ($v[0] ^ $v[1]));
            $tests[] = array($e,$cross);
          }
        }     
      }
    }
    return $tests;
  }
  /**
   * @dataProvider getFull3D
   */
  public function testFull3D(array $subspaces,$cross){
    $framework = $this->setTimingFramework();
    $framework
      ->expects($this->once())
      ->method('get_time')
      ->will($this->returnValue(10));

    $key = $subspaces[0];
    $cv = $this->getSUT(array('x','y','z'));
    $v1 = $cv->get_version($key);
    
    $updated = $subspaces[1];
    $cv->invalidate($updated);
    $v2 = $cv->get_version($key);
    //$this->assertInternalType('int',$v2);
    if($cross){
      $this->assertTrue($v1 < $v2);
    }else{
      $this->assertSame($v1,$v2);
    }
  }
}
?>
