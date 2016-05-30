<?php
class StringsTest extends FrameworkTestCase
{
  public function getIsPrefixOfData(){
    return array(
      array('a','ala',true),
      array('ala','ala',true),
      array('alan','ala',false),
      array('','ala',true),
      array('ala','',false),
      array('ż','że',true),
      array('żółw','żółwik',true),
      array('b','ala',false),
    );
  }
  /**
   * @dataProvider getIsPrefixOfData
   */
  public function testIsPrefixOf($short,$long,$is_it){
    $this->assertSame($is_it, Strings::is_prefix_of($short,$long));
  }
  public function getLenData(){
    return array(
      array("żuk",3),
      array("",0),
      array("zażółć",6),
    );
  }
  /**
   * @dataProvider getLenData
   */
  public function testLen($text,$expectedLen){
    $this->assertSame($expectedLen, Strings::len($text));
  }
}
?>
