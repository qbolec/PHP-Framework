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
}
?>
