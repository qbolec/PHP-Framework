<?php
class MissingKeyConfigExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = new MissingKeyConfigException('ojej');
    $this->assertInstanceOf('IsMissingException',$a);
  }
}
?>
