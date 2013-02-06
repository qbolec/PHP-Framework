<?php
class AbstractConfigTest extends PHPUnit_Framework_TestCase
{
  private function fixedConfig($tree){
    $config = $this->getMock('AbstractConfig',array('get_tree'));
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue($tree));
    return $config;
  }
  private function emptyConfig(){
    return $this->fixedConfig(array());
  }
  private function nonEmptyConfig(){
    return $this->fixedConfig(array(
      'ah'=>array(
        1 => 'a-one',
        2 => 'a-two',
      ),
      'b'=>array(
        'cool' => array(
        ),
        'd' => array(
          'e' => 123,
        ),
      )
    ));
  }
  public function testEmptyHasEmptyTree(){
    $config = $this->emptyConfig();
    $this->assertEquals(array(), $config->get(''));
  }
  /**
   * @dataProvider goodKeys
   * @expectedException MissingKeyConfigException
   */
  public function testEmptyDoesNotHave($good_key){
    $config = $this->emptyConfig();
    $config->get($good_key);
  }
  public function goodKeys(){
    return array(
      array('ala'),
      array('a/la'),
      array('a/l/a'),
    );
  }
  /**
   * @dataProvider badKeys
   * @expectedException LogicException
   */
  public function testEmptyValidatesPath($bad_key){
    $config = $this->emptyConfig();
    $config->get($bad_key);
  }
  public function badKeys(){
    return array(
      array('/'),
      array('a//b'),
      array('a/'),
      array('a/b/'),
      array('/a/b'),
    );
  }
  /**
   * @dataProvider keyValues
   */
  public function testNonEmptyWorks($key,$value){
    $config = $this->nonEmptyConfig();
    $this->assertEquals($value,$config->get($key));
  }
  public function keyValues(){
    return array(
      array('',array(
        'ah'=>array(
          1 => 'a-one',
          2 => 'a-two',
        ),
        'b'=>array(
          'cool' => array(
          ),
          'd' => array(
            'e' => 123,
          ),
        ),
      )),
      array('ah',array(
        1 => 'a-one',
        2 => 'a-two',
      )),
      array('ah/1','a-one'),
      array('ah/2','a-two'),
      array('b',array(
        'cool' => array(
        ),
        'd' => array(
          'e' => 123,
        ),
      )),
      array('b/cool',array()),
      array('b/d',array('e'=>123)),
      array('b/d/e',123),
    );
  }
  /**
   * @dataProvider badKeys
   * @expectedException LogicException
   */
  public function testNonEmptyValidatesPath($bad_key){
    $config = $this->nonEmptyConfig();
    $config->get($bad_key); 
  }
  /**
   * @dataProvider missingKeys
   * @expectedException MissingKeyConfigException
   */
  public function testNonEmptyHasMisses($missing_key){
    $config = $this->nonEmptyConfig();
    $config->get($missing_key);
  }
  public function missingKeys(){
    return array(
      array('a/3'),
      array('b/c/0'),
      array('b/d/f'),
      array('c'),
    );
  }
}
?>
