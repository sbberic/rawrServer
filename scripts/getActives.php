<?php

//note to consider to fix later
//in the time between this is called and the tcp socket connects, someone could join without the client knowing. 
require_once 'lib/redisConfig.php';
$redis = new Predis\Client($single_server);

$lid=$_GET['lid'];
$actives = $redis->smembers('loc:'.$lid.'.active');
$postData = array();
foreach ($actives as $uid){

	$nextPost = array($pc => array('uid' => $uid));
	$postData = array_merge($postData, $nextPost);
	$pc++;

}
$locPosts = json_encode($postData);
echo stripslashes($locPosts);

?>
