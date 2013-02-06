<?php
require_once 'autoload.php';
$T = Arrays::get($_SERVER['argv'],1,1000);
echo "This test requires users with ids from [1, $T] to exist in the system\nRun it several times to make sure memcache is hot.\n";
$users_module = UsersModule::get_instance();
$users_ids = range(1,$T);
$start = microtime(true);
$persons_ids = $users_module->multi_get_persons_ids_by_users_ids($users_ids);
$end = microtime(true);
$duration = $end-$start;

printf("Results count\t: %d\nDuration\t: %0.3f sec\n",count($persons_ids),$duration); 
?>
