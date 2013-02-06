<?php
class SQLHelperTest extends FrameworkTestCase
{
  /**
   * @dataProvider fieldsData
   */
  public function testFields(array $data,$expected){
    $this->assertSame($expected,SQLHelper::fields($data));
  }
  public function fieldsData(){
    return array(
      array(array('a'=>'b'),'`a`'),
      array(array(),''),
      array(array('a'=>'b','b'=>1),'`a`,`b`'),
    );
  }
  /**
   * @dataProvider fieldsMatchPlacerholdersData
   */
  public function testFieldsMatchPlaceholders(array $data,$expected){
    $this->assertSame($expected,SQLHelper::fields_match_placeholders($data,'<=>'));
  }
  public function fieldsMatchPlacerholdersData(){
    return array(
      array(array('a'=>'b'),'`a`<=>:a'),
      array(array(),''),
      array(array('a'=>'b','b'=>1),'`a`<=>:a AND `b`<=>:b'),
      array(array('a'=>'b','b'=>null),'`a`<=>:a AND `b`<=>:b'),
    );
  }

  /**
   * @dataProvider assignPlaceholdersToFieldsData
   */
  public function testAssignPlaceholdersToFields(array $data,$expected){
    $this->assertSame($expected,SQLHelper::assign_placeholders_to_fields($data));
  }
  public function assignPlaceholdersToFieldsData(){
    return array(
      array(array('a'=>'b'),'`a`=:a'),
      array(array(),''),
      array(array('a'=>'b','b'=>1),'`a`=:a,`b`=:b'),
    );
  }

  /**
   * @dataProvider placeholdersData
   */
  public function testPlaceholders(array $data,$expected){
    $this->assertSame($expected,SQLHelper::placeholders($data));
  }
  public function placeholdersData(){
    return array(
      array(array('a'=>'b'),':a'),
      array(array(),''),
      array(array('a'=>'b','b'=>1),':a,:b'),
    );
  }
 


}
?>
