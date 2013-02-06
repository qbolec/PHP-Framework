<?php
class BigNumsTest extends FrameworkTestCase
{
  public function test_precission(){
    $a = pow(2,52);
    $b = $a+1;
    $c = $a-1;
    $this->assertEquals(-1,$c-$a);
    $this->assertEquals(1,$a-$c);
    $this->assertEquals(1,$b-$a);
    $this->assertEquals(-1,$a-$b);
  }
  public function test_add_mul_consistence(){
    $a = pow(2,30);
    $b = $a+$a+$a+$a+$a+$a+$a+$a;
    $c = $a*8;
    $this->assertSame($b,$c);
  }
  public function test_shift_left_inconsistence(){
    $a = pow(2,30);
    $b = $a*8;
    $d = $a<<3;
    $this->assertNotSame($d,$b);
  }
  public function test_shift_right_inconsistence(){
    $a = pow(2,33);
    $b = $a/8;
    $d = $a>>3;
    $this->assertNotSame($d,$b);
  }
  public function test_right_shift_emulation(){
    $a = pow(2,50) + 0x1234;
    $b = $a >> 8;
    $c = BigNums::shr($a,8);
    $this->assertNotSame($b,$c);
    $this->assertSame(0x12, $c & 0xFFFF);
    $this->assertSame($a, $c * pow(2,8)+0x34);
  }
  public function test_real_case_scenario(){
    $bits = array(13,10,12,7,7);
    for($t=100;$t--;){
      $xs = array();
      $a = 0.0;
      foreach($bits as $b){
        $x = rand(1,(1<<$b)-1);
        $a = BigNums::shl($a,$b);
        $a += $x;
        $xs[] = $x; 
      }
      $ys = array();
      foreach(array_reverse($bits) as $b){
        $y = BigNums::lsbits($a,$b);
        $ys[] = $y;
        $a = BigNums::shr($a,$b);
      }
      $this->assertSame($xs,array_reverse($ys));
    }
  }
}
?>
