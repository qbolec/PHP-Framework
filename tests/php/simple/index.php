<?php
include_once('test_bootstrap.php');
//ten plik ma za zadanie jakoś tam przetestować index.php
//trudne zadanie, bo jeszcze nie mamy zbyt wielu narzędzi do mockowania, ale powinno sie udać.
//Spodziewamy się w zasadzie tylko jednego: że zostanie wywołane run.
//Setup: Tworzę więc lipną aplikację, która mierzy liczbę wywołań run.
class BetterApplication{
  public static $ok=0;
  public static function get_instance(){
    return new self();
  }
  public function run(){
    self::$ok++;
  } 
}
//Test: Wywołuję index.php
require_once '../../../htdocs/index.php';
//Check: Sprawdzam, czy wowołało sie raz
my_assert(BetterApplication::$ok == 1);
?>
