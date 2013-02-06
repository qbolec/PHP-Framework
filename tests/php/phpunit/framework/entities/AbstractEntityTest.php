<?php
//get_family is called in constructor, so we can not easily mock it
class FakeEntity extends AbstractEntity
{
  public static $family;
  public function get_family(){
    return self::$family;
  }
}
class AbstractEntityTest extends PHPUnit_Framework_TestCase
{
  private function justIdFamily(){
    $family = $this->getMock('IEntities');
    $family
      ->expects($this->any())
      ->method('get_fields_descriptor')
      ->will($this->returnValue(FieldsDescriptorFactory::get_instance()->get_from_array(array(
        'id' => new IntFieldType(),
      ))));
    return $family;
  } 
  public function testInterface(){
    FakeEntity::$family = $this->justIdFamily();
    $data = array(
      'id' => 42,
    );
    $a = new FakeEntity($data);
    $this->assertInstanceOf('IEntity',$a);
    $this->assertSame(42,$a->get_id());
  }
  /**
   * @expectedException IValidationException
   */
  public function testValidation(){
    FakeEntity::$family = $this->justIdFamily();
    $data = array(
      'id' => true,
    );
    $a = new FakeEntity($data);
  }
  /**
   * @expectedException LogicException
   */
  public function testMissing(){
    FakeEntity::$family = $this->justIdFamily();
    $data = array();
    $a = new FakeEntity($data);
  }
  /**
   * @expectedException LogicException
   */
  public function testTooMuch(){
    FakeEntity::$family = $this->justIdFamily();
    $data = array(
      'id' => 42,
      'whatever' => null,
    );
    $a = new FakeEntity($data);
  }
}
?>
