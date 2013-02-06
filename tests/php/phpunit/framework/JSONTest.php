<?php
class JSONTest extends FrameworkTestCase
{
  /**
   * @dataProvider getEncodeTests
   */
  public function testEncode($data,$expected_output){
    $this->assertSame($expected_output, JSON::encode($data));
    $this->assertSame($data, JSON::decode($expected_output));
  }
  public function getEncodeTests(){
    return array(
      array(null,'null'),
      array(1,'1'),
      array(1.0,'1.0'),
      array(true,'true'),
      array('1','"1"'),
      array(array(),'[]'),
      array(array('a'=>'b'),'{"a":"b"}'),
      array(array(0=>'a',1=>'b'), '["a","b"]'),
      array(array(1=>'a',2=>'b'), '{"1":"a","2":"b"}'),
      array(array(42=>42), '{"42":42}'),
      array(array(42), '[42]'),
    );
  }
  /**
   * @dataProvider getBad
   * @expectedException CouldNotConvertException
   */ 
  public function testBad($bad){
    JSON::decode($bad);
  }
  public function getBad(){
    return array(
      array(''),
      array('N'),
      array('bzdura'),
      array(null),
      array('{a:b}'),
    );
  }
  /**
   * @dataProvider forceData
   */
  public function testForceAssoc($data, $expected_output){
    $this->assertSame($expected_output, JSON::encode(JSON::force_assoc($data)));
  }
  public function forceData(){
    return array(
      array(array(), '{}'),
      array(array(1, 2, 3), '{"0":1,"1":2,"2":3}'),
      array(array('a','b'), '{"0":"a","1":"b"}'),
      array(array(0=>'a',1=>'b'), '{"0":"a","1":"b"}'),
      array(array('a'=>'b'),'{"a":"b"}'),
    );
  }
}
?>
