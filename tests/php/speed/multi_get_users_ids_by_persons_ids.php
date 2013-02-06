<?php
require_once 'autoload.php';
$T = Arrays::get($_SERVER['argv'],1,1000);
echo "This test requires users with persons ids : fake.X from [0, $T) to exist in the system\nYou can assure that by using summon_user.php\nRun it several times to make sure memcache is hot.\n";
$users_module = UsersModule::get_instance();
$persons_ids = array();
for($i=0;$i<$T;++$i){
  $persons_ids[] = 'fake.' . $i;
}
$start = microtime(true);
$map = $users_module->multi_get_users_ids_by_persons_ids($persons_ids);
$end = microtime(true);
$duration = $end-$start;

printf("Results count\t: %d\nDuration\t: %0.3f sec\n",count($map),$duration); 
?>
