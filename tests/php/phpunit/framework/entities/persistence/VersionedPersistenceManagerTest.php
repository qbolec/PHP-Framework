<?php
class VersionedPersistenceManagerTest extends FrameworkTestCase
{
  private function getSUT(){
    $pm = $this->getMock('IPersistenceManager');
    $versioning = $this->getMock('ICacheVersioning');
    return array(new VersionedPersistenceManager($pm,$versioning),$pm,$versioning);
  }
  public function testInterface(){
    list($sut)=$this->getSUT();
    $this->assertInstanceOf('IPersistenceManager',$sut);
  }
  /**
   * @dataProvider getBool
   */
  public function testDeleteById($success){
    $id = 4;
    list($sut,$pm,$versioning)=$this->getSUT();
    $pm
      ->expects($this->once())
      ->method('delete_by_id')
      ->with($this->equalTo($id))
      ->will($this->returnValue($success));
    if($success){
      $versioning
        ->expects($this->once())
        ->method('invalidate')
        ->with($this->equalTo(array('id'=>$id)));
    }else{
      $versioning
        ->expects($this->never())
        ->method('invalidate');
    }
    $this->assertSame($success,$sut->delete_by_id($id));
  }
  /**
   * @dataProvider getBool
   */
  public function testInsert($success){
    $data = array(
      'id'=>4,
      'sex'=>true,
    );
    list($sut,$pm,$versioning)=$this->getSUT();
    $pm
      ->expects($this->once())
      ->method('insert')
      ->with($this->equalTo($data))
      ->will($this->returnValue($success));
    if($success){
      $versioning
        ->expects($this->once())
        ->method('invalidate')
        ->with($this->equalTo($data));
    }else{
      $versioning
        ->expects($this->never())
        ->method('invalidate');
    }
    $this->assertSame($success,$sut->insert($data));
  }
  public function testInsertAndAssignId(){
    $data = array(
      'sex'=>true,
    );
    $id = 4;
    list($sut,$pm,$versioning)=$this->getSUT();
    $pm
      ->expects($this->once())
      ->method('insert_and_assign_id')
      ->with($this->equalTo($data))
      ->will($this->returnValue($id));
    $versioning
      ->expects($this->once())
      ->method('invalidate')
      ->with($this->equalTo(array_merge($data,array('id'=>$id))));
    $this->assertSame($id,$sut->insert_and_assign_id($data));
  }
  /**
   * @dataProvider getBool
   */
  public function testSave($success){
    $data = array(
      'id'=>4,
      'sex'=>true,
    );
    $old = array(
      'sex'=>false,
    );
    list($sut,$pm,$versioning)=$this->getSUT();
    $pm
      ->expects($this->once())
      ->method('save')
      ->with($this->equalTo($data),$this->equalTo($old))
      ->will($this->returnValue($success));
    if($success){
      $versioning
        ->expects($this->exactly(2))
        ->method('invalidate')
        ->with($this->logicalOr($this->equalTo($data),$this->equalTo($old+$data)));

    }else{
      $versioning
        ->expects($this->never())
        ->method('invalidate');
    }
    $this->assertSame($success,$sut->save($data,$old));
  }
}
?>
