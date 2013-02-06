<?php
abstract class AbstractEditableEntityImpl extends AbstractEditableEntity
{
  private $family;
  public function __construct(array $data,IEntities $family){
    $this->family = $family;
    parent::__construct($data);
  }
  public function get_family(){
    return $this->family;
  }
  public function set_field($field_name,$value){
    return parent::set_field($field_name,$value);
  }
}

class AbstractEditableEntityTest extends FrameworkTestCase
{
  private function getFamily(){
    $d = $this->getMock('IFieldsDescriptor');
    $d
      ->expects($this->any())
      ->method('get_description')
      ->will($this->returnValue(array(
        'id' => new IntFieldType(),
        'a' => new StringFieldType(),
      )));
    $d
      ->expects($this->any())
      ->method('get_validator')
      ->will($this->returnValue(new RecordValidator(array(
        'id' => new IntValidator(),
        'a' => new StringValidator(),
      ))));
    $f = $this->getMock('IEditableEntities');
    $f
      ->expects($this->any())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($d));
    $this->assertInstanceOf('IFieldsDescriptor',$f->get_fields_descriptor());
    return $f;
  }
  private function getData(){
    return array(
      'id' => 42,
      'a' => 'b',
    );
  }
  private function getSUT(array $data,IEntities $family){
    return $this->getMockForAbstractClass('AbstractEditableEntityImpl',array($data,$family));
  }
  public function testInterface(){
    $a = $this->getSUT($this->getData(),$this->getFamily());
    $this->assertInstanceOf('IEditableEntity',$a);
  }
  
  /*
   * @expectedException LogicException
   *//*
  public function testCommitChecksCount(){
    $a = $this->getSUT($this->getData(),$this->getFamily());
    $a->commit();
  }
  
  public function testCommitWaitsForAll(){
    $data = $this->getData();
    $family = $this->getFamily();
    $family
      ->expects($this->never())
      ->method('save');
    $family
      ->expects($this->once())
      ->method('get_fresh_data')
      ->with($this->equalTo($data['id']))
      ->will($this->returnValue($data));
    $a = $this->getSUT($data,$family);
    $a->begin();
    $a->begin();
    $a->commit();
  }
  public function testCommitCalssSave(){
    $data = $this->getData();
    $family = $this->getFamily();
    $family
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo($data),$this->equalTo(array()));
    $family
      ->expects($this->once())
      ->method('get_fresh_data')
      ->with($this->equalTo($data['id']))
      ->will($this->returnValue($data));

    $a = $this->getSUT($data,$family);
    $lock = $a->begin();
    $a->begin();
    $a->commit();
    $a->commit($lock);
  }*/
  public function testCommitPassesChangedData(){
    $data = $this->getData();
    $family = $this->getFamily();
    $family
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo(array('id'=>42,'a'=>'B')),$this->equalTo(array('a'=>'b')));
    $family
      ->expects($this->once())
      ->method('get_fresh_data')
      ->with($this->equalTo($data['id']))
      ->will($this->returnValue($data));

    $a = $this->getSUT($data,$family);
    $a->set_field('a','B');
  }
  public function testTwoTransactionsDontCollide(){
    $data = array(
      'id' => 42,
      'a' => 'a',
      'b' => 'b',
    );

    $storage = $data;

    $d = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'id' => new IntFieldType(),
      'a' => new StringFieldType(),
      'b' => new StringFieldType(),
    ));
    
    $family = $this->getMock('IEditableEntities');
    $family
      ->expects($this->any())
      ->method('get_fields_descriptor')
      ->will($this->returnValue($d));
    $family
      ->expects($this->exactly(2))
      ->method('save')
      ->will($this->returnCallback(function (array $new,array $old) use(&$storage){
        $storage = $new;
      }));

    $family
      ->expects($this->exactly(2))
      ->method('get_fresh_data')
      ->with($this->equalTo(42))
      ->will($this->returnCallback(function ($id) use(&$storage){return $storage;}));
     
    $a = $this->getSUT($data,$family);
    $b = $this->getSUT($data,$family);

    $a->set_field('a','A');
    $b->set_field('b','B');

    $this->assertEquals(array('id'=>42,'a'=>'A','b'=>'B'),$storage);
  }

}
?>
