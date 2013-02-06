<?php

class EmptyEnvTest extends PHPUnit_Framework_TestCase
{
  public function testConstructor(){
    $env = new EmptyEnv();
    $this->assertInstanceOf('IEnv',$env);
  }
}
?>
