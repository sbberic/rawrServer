<?php
require_once 'lib/redisConfig.php';
$redis = new Predis\Client($single_server);
$pid= $redis->get('global.pid');
echo $pid;

?>
