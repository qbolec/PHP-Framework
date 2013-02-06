<?php
class FrameworkTestCase extends PHPUnit_Framework_TestCase
{
  private $mocked = array();
  protected function set_global_mock($class_name,$mock){
    if(array_key_exists($class_name,$this->mocked)){
      throw new TooMuchMockingException();
    }else{
      $this->mocked[$class_name] = $class_name::set_instance($mock);
    }
  }
  protected function get_test_pdo_config(){
    return array(
      'dsn' => 'mysql:dbname=test;host=127.0.0.1',
      'username' => 'nk_test',
      'password' => 'ohnOHIGsmxCglIZ8edLc',
    );
  }
  protected function getPDO(){
    $c = $this->get_test_pdo_config();
    return new PDO($c['dsn'],$c['username'],$c['password']);
  }
  protected function setConfig(array $tree){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue($tree));
    $this->set_global_mock('Config',$config);
  }
  protected function setMockery($mock,array $mockery,$count='atLeastOnce'){
    foreach($mockery as $foo => $val){
      $mock
        ->expects($this->$count())
        ->method($foo)
        ->will($this->returnValue($val));
    }
  }
  public function tearDown(){
    foreach($this->mocked as $class_name => $original){
      $class_name::set_instance($original);
      unset($this->mocked[$class_name]);
    }
    SingletonFlusher::get_instance()->flush();
  }
  public static function assertIsPermutationOf(array $correct,array $tested, $message = ''){
    self::assertThat($tested,self::isPermutationOf($correct),$message);
  }
  public static function isPermutationOf(array $correct){
    return new IsPermuationOf($correct);
  } 
  protected function getUserlikeFieldsDescriptor(){
    return FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'id' => new IntFieldType(),
      'person_id' => new StringFieldType(),
    ));
  }
  public function getZeroOrOne(){
    return array(
      array(0),
      array(1),
    );
  }
  public function getBool(){
    return array(
      array(false),
      array(true),
    );
  }
}
?>
