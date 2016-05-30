<?php

class ArraysTest extends FrameworkTestCase
{
  public function mergeData(){
    return array(
      array(array(),array(),array()),
      array(array(),array('ala'),array('ala')),
      array(array(),array('a'=>'ala'),array('a'=>'ala')),
      array(array('ala'),array('ala'),array('ala')),
      array(array('ala'),array(),array('ala')),
      array(array('a'=>'ala'),array(),array('a'=>'ala')),
      array(array('ala'),array('kot'),array('kot')),
      array(array('ala'),array(1=>'kot'),array('ala','kot')),
      array(array('a'=>'ala'),array('a'=>'kot'),array('a'=>'kot')),
      array(array('a'=>'ala'),array('b'=>'kot'),array('a'=>'ala','b'=>'kot')),
      array(array('a'=>'ala',0=>'As'),array('b'=>'kot',0=>'Bas'),array('a'=>'ala','b'=>'kot',0=>'Bas')),
      array(array('a'=>'A',0=>'A'),array('b'=>'A',1=>'A'),array('a'=>'A','b'=>'A',0=>'A',1=>'A')),
    );
  }
  /**
   * @dataProvider mergeData
   */
  public function testMerge($a,$b,$ab){
    $x=Arrays::merge($a,$b);
    $this->assertEquals($ab,$x);
  }

  public function testGet(){
    $a = array('a','b');
    $this->assertEquals('a',Arrays::get($a,0));
    $this->assertEquals('a',Arrays::get($a,'0'));
    $this->assertEquals('b',Arrays::get($a,1));
    $this->assertEquals(null,Arrays::get($a,2));
    $this->assertEquals('c',Arrays::get($a,2,'c'));
    $this->assertEquals(null,Arrays::get($a,null));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testGrabMiss(){
    $a = array('a'=>'b');
    Arrays::grab($a,0);
  }
  public function testGrabHit(){
    $a = array('a'=>'b');
    $this->assertSame('b',Arrays::grab($a,'a'));
  }
  /**
   * @dataProvider concatData
   */
  public function testConcat($a,$b,$ab){
    $this->assertSame($ab,Arrays::concat($a,$b));
  }
  public function concatData(){
    return array(
      array(array(),array(),array()),
      array(array('a'),array(),array('a')),
      array(array(),array('b'),array('b')),
      array(array('a'),array('b'),array('a','b')),
    );
  }
  /**
   * @dataProvider transposeData
   */
  public function testTranspose($ab,$ba){
    $this->assertEquals($ba,Arrays::transpose($ab));
    $this->assertEquals($ab,Arrays::transpose($ba));
  }
  public function transposeData(){
    return array(
      array(array(),array()),
      array(array(array(42)),array(array(42))),
      array(array(array(42,43)),array(array(42),array(43))),
      array(array(array(11,12),array(21,22)),array(array(11,21),array(12,22))),
      array(array(array('x'=>11,'y'=>12),array('x'=>21,'y'=>22)),array('x'=>array(11,21),'y'=>array(12,22))),
      array(array('A'=>array('x'=>11,'y'=>12),'B'=>array('x'=>21,'y'=>22)),array('x'=>array('A'=>11,'B'=>21),'y'=>array('A'=>12,'B'=>22))),
      array(array('A'=>array('x'=>11,'z'=>12),'B'=>array('x'=>21,'y'=>22)),array('x'=>array('A'=>11,'B'=>21),'y'=>array('B'=>22),'z'=>array('A'=>12))),
    );
  }
  /**
   * @dataProvider mapAssocData
   */
  public function testMapAssoc(array $arr,array $expected){
    $this->assertEquals($expected,Arrays::map_assoc(function($key,$value){return $key . $value;},$arr));
  }
  public function mapAssocData(){
    return array(
      array(array(),array()),
      array(array('a'=>'1'),array('a'=>'a1')),
      array(array('a'=>'1','b'=>'2'),array('a'=>'a1','b'=>'b2')),
    );
  }
  /**
   * @dataProvider allSubsetsData
   */
  public function testAllSubsets(array $arr,array $expected){
    $this->assertIsPermutationOf($expected,Arrays::all_subsets($arr));
  }
  public function allSubsetsData(){
    return array(
      array(array(),array(array())),
      array(array(42),array(array(),array(42))),
      array(array(42,43),array(array(),array(0=>42),array(1=>43),array(1=>43,0=>42))),
    );
  }
  /**
   * @dataProvider insertData
   */
  public function testInsert(array $input_array,$index,$element,array $expected){
    $this->assertSame($expected,Arrays::insert($input_array, $index, $element));
  }
  public function insertData(){
    return array(
       array(array(), 0, 42, array(42)),
       array(array(1, 2, 3), 0, 42, array(42, 1, 2, 3)),
       array(array(1, 2, 3), 2, 42, array(1, 2, 42, 3)),
       array(array(1, 2, 3), 3, 42, array(1, 2, 3, 42)),
       array(array(1, 2, 3), 10, 42, array(1, 2, 3, 42)),
       array(array(1, 2, 3), -1, 42, array(1, 2, 42, 3)),
       array(array(1, 2, 3), -3, 42, array(42, 1, 2, 3)),
       array(array(1, 2, 3), -10, 42, array(42, 1, 2, 3)),
    );
  }
  /**
   * @dataProvider combineData
   */
  public function testCombine(array $keys, array $values, array $expected){
    $this->assertSame($expected, Arrays::combine($keys, $values));
  }
  public function combineData(){
    return array(
      array(array(),array(),array()),
      array(array(0),array('value'),array('value')),
      array(array('key'), array('value'), array('key' => 'value')),
      array(array('key1','key2'), array('value1','value2'), array('key1' => 'value1','key2' => 'value2')),
    );
  }
  /**
   * @dataProvider unionData
   */
  public function testUnion(array $keys, array $values, array $expected){
    $this->assertIsPermutationOf($expected, Arrays::union($keys, $values));
  }
  public function unionData(){
    return array(
      array(array(),array(),array()),
      array(array(),array(1),array(1)),
      array(array(1), array(), array(1)),
      array(array(1), array(2), array(1,2)),
      array(array(1), array(2), array(2,1)),
      array(array(1,2,3), array(1,2,3), array(3,2,1)),
      array(array(1,3), array(2,4), array(1,2,3,4)),
      array(array(1,'trzy'), array(2,'cztery'), array('trzy','cztery',2,1)),
    );
  }
  /**
   * @dataProvider getIntersectKeyData
   */
  public function testIntersectKey(array $a,array $b,array $expected_result){
    $this->assertSame($expected_result,Arrays::intersect_key($a,$b));
  }
  public function getIntersectKeyData(){
    return array(
      array( array(),array(),array() ),
      array( array(),array('a'=>'A'),array() ),
      array( array('a'=>'B'),array('a'=>'A'),array('a'=>'B') ),
      array( array('a'=>null),array('a'=>null),array('a'=>null) ),
      array( array('a'=>'A'),array('a'=>null),array('a'=>'A') ),
      array( array('b'=>'B','c'=>'C'),array('a'=>'A'),array() ),
      array( array('a'=>null,'b'=>'B','c'=>'C'),array('a'=>'A'),array('a'=>null) ),
      array( array('a'=>null,'b'=>'B','c'=>'C'),array('a'=>'A','b'=>null),array('a'=>null,'b'=>'B') ),
    );
  }
  public function getSetKeysOrderData(){
    return array(
      array( array(), array(), array(), array() ),
      array( array(), array(12), array(), array() ),
      array( array(10 => 2, 12=>3, 11=>4), array(12), array(12), array(3) ),
      array( array(10 => 2, 12=>3, 11=>4), array(10,12), array(10,12), array(2,3) ),
      array( array(10 => 2, 12=>3, 11=>4), array(12,10), array(12,10), array(3,2) ),
      array( array(10 => 2, 12=>3, 11=>4), array(12,10,15), array(12,10), array(3,2) ),
    );
  }
  /**
   * @dataProvider getSetKeysOrderData
   */
  public function testSetKeysOrder($arr,$keys_order,$expected_keys,$expected_values){
    $ordered = Arrays::set_keys_order($arr,$keys_order);
    $this->assertSame($expected_keys, array_keys($ordered));
    $this->assertSame($expected_values, array_values($ordered));
  }
  public function getInflateData(){
    return array(
      array(array(),array()),
      array(array(2,1),array(array(2),array(1))),
      array(array('a'=>1,'b'=>2),array('a'=>array(1),'b'=>array(2))),
    );
  }
  /**
   * @dataProvider getInflateData
   */
  public function testInflate($in,$out){
    $this->assertSame($out,Arrays::inflate($in));
  }

  public function getFlattenData(){
    return array(
      array(array(),array()),
      array(array(array(2),array(1)),array(2,1)),
      array(array('a'=>array(1,5),'b'=>array(2,3)),array(1,5,2,3)),
    );
  }
  /**
   * @dataProvider getFlattenData
   */
  public function testFlatten($in,$out){
    $this->assertSame($out,Arrays::flatten($in));
  }
  public function getRangeData(){
    return array(
      array(1,1,array()),
      array(1,2,array(1)),
      array(1,0,array()),
      array(1,3,array(1,2)),
    );
  }
  /**
   * @dataProvider getRangeData
   */
  public function testRange($from,$to,$expected){
    $this->assertSame($expected,Arrays::range($from,$to));
  }
  public function getDiffAssocData(){
    return array(
      array(array('a'=>'a','0'=>'0',''=>'','null'=>null),array(),array('a'=>'a','0'=>'0',''=>'','null'=>null)),
      array(array('a'=>'a','0'=>'0',''=>'','null'=>null),array(''=>null),array('a'=>'a','0'=>'0',''=>'','null'=>null)),
      array(array('a'=>'a','0'=>'0',''=>'','null'=>null),array('0'=>0),array('a'=>'a','0'=>'0',''=>'','null'=>null)),
      array(array('a'=>'a','0'=>'0',''=>'','null'=>null),array(''=>''),array('a'=>'a','0'=>'0','null'=>null)),
      array(array('a'=>'a','0'=>'0',''=>'','null'=>null),array('0'=>'0'),array('a'=>'a',''=>'','null'=>null)),
      array(array('a'=>'a','0'=>'0',''=>'','null'=>null),array('a'=>'b'),array('a'=>'a','0'=>'0',''=>'','null'=>null)),
      array(array(),array(7),array()),
      array(array(10,20,30),array(1=>20),array(0=>10,2=>30)),
    );
  }
  /**
   * @dataProvider getDiffAssocData
   */
  public function testDiffAssoc($a,$b,$diff){
    $this->assertSame($diff,Arrays::diff_assoc($a,$b));
  }
  public function getWithoutData(){
    return array(
      array( array('a','0','',0,null,false,'a'), 'b', array('a','0','',0,null,false,'a')),
      array( array('a','0','',0,null,false,'a'), 'a', array('0','',0,null,false)),
      array( array('a','0','',0,null,false,'a'), '0', array('a','',0,null,false,'a')),
      array( array('a','0','',0,null,false,'a'), '', array('a','0',0,null,false,'a')),
      array( array('a','0','',0,null,false,'a'), 0, array('a','0','',null,false,'a')),
      array( array('a','0','',0,null,false,'a'), null, array('a','0','',0,false,'a')),
      array( array('a','0','',0,null,false,'a'), false, array('a','0','',0,null,'a')),
    );
  }
  /**
   * @dataProvider getWithoutData
   */
  public function testWithout($a,$e,$rest){
    $this->assertSame($rest,Arrays::without($a,$e));
  }

  public function getLastData(){
    return array(
      array(array(0,1,2),2),
      array(array(1=>0,2=>1,3=>2),2),
      array(array(3=>0,2=>1,1=>2),2),
      array(array(3=>0,"x"=>1,"aa"=>2),2),
    );
  }
  /**
   * @dataProvider getLastData
   */
  public function testLast($a,$e){
    $this->assertSame($e,Arrays::last($a));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testLastOfEmpty(){
    Arrays::last(array());
  }
}
?>
