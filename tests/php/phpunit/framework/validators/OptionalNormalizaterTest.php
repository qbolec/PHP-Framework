<?php
class OptionalNormalizerTest extends FrameworkTestCase
{
  private function getSUT($inner){
    return new OptionalNormalizer($inner);
  }

  public function testInterface(){
    $inner = $this->getMock('INormalizer');
    $validator = $this->getSUT($inner);
    $this->assertInstanceOf('INormalizer',$validator);
  }

  public function testNull(){
    $inner = $this->getMock('INormalizer');
    $inner
      ->expects($this->never())
      ->method('normalize');
    $validator = $this->getSUT($inner);
    $this->assertSame(null,$validator->normalize(null));
  }
  public function testOk(){
    $value = '42';
    $normalized = 42;
    $inner = $this->getMock('INormalizer');
    $inner
      ->expects($this->atLeastOnce())
      ->method('normalize')
      ->with($value)
      ->will($this->returnValue($normalized));
    $validator = $this->getSUT($inner);
    $this->assertSame($normalized,$validator->normalize($value));
  }
  /**
   * @expectedException CouldNotConvertException
   */
  public function testBad(){
    $value = 13;
    $e = new CouldNotConvertException($value);
    $inner = $this->getMock('INormalizer');
    $inner
      ->expects($this->atLeastOnce())
      ->method('normalize')
      ->with($value)
      ->will($this->throwException($e));
    $validator = $this->getSUT($inner);
    $validator->normalize($value);
  }
}
?>
