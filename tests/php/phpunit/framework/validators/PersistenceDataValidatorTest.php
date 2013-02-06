<?php
class PersistenceDataValidatorTest extends FrameworkTestCase
{
  public function testInstance(){
    $p = new PersistenceDataValidator(array());
    $this->assertInstanceOf('IValidator',$p);
  }
  public function testIsValid(){
    $d = array(
      'id' => new IntFieldType(),
      'x' => new StringFieldType(),
    );
    $p = new PersistenceDataValidator($d);
    $this->assertSame(true,$p->is_valid(array('id'=>42,'x'=>'y')));
    $this->assertSame(false,$p->is_valid(array('id'=>42,'x'=>'y','whatever'=>'else')));
    $this->assertSame(false,$p->is_valid(array('x'=>'y')));
    $this->assertSame(false,$p->is_valid(array('id'=>'42','x'=>'y')));
  }
}
?>
