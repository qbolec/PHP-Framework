<?php
//ten test nie jest napisany pod PHPUnit 
//bo ma przetestować wysyłanie nagłówków,
//co niestety kończy się klęską, gdy cokolwiek zostało już wysłane
//a że PHPUnit zaczyna od wypisania na ekran copyrightów, to niestety
//nigdy nie udaje sie wysłac nagłówków
//
require_once('autoload.php');
$output = new HTTPOutput();
$output->send_status(200,'OK');
$output->send_status(404,'NotFound');
$output->send_header('Key','value');
$output->send_body('OK');
$output->send_body("\n");
//wysyłanie nagłówków po wysłaniu body nie powinno się udać
try{
  $output->send_header('Key','value');
  echo "NOT OK\n";
  exit(1);
}catch(LogicException $e){
  echo "OK\n";
}
//podobnie zmiana statusu
try{
  $output->send_status(404,'Not found');
  echo "NOT OK\n";
  exit(1);
}catch(LogicException $e){
  echo "OK\n";
}
?>
