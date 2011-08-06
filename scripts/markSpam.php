<?php

require_once 'lib/redisConfig.php';
$pid = $_POST['pid'];

$redis = new Predis\Client($single_server);

$redis->incr("post:".$pid.".spam");
$spamCount = $redis->get("post:".$pid.".spam");

if($spamCount > 3){ //spam sensitivity threshold
	$redis->set("post:".$pid.".spam", -1);
} 

?>