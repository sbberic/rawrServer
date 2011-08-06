<?php
require_once 'lib/redisConfig.php';

$doer=$_POST['uid1']; 
$receiver=$_POST['uid2'];

$redis = new Predis\Client($single_server);

$doerFriends ='uid:'.$doer.'.friends';
$receiverFriends ='uid:'.$receiver.'.friends';
$relationship = $redis->zscore($doerFriends, $receiver);
if($relationship == 1){
	$redis->zadd($doerFriends, $receiver, 3);
	$redis->zadd($receiverFriends, $doer, 3);
}
else{
	$redis->zadd($doerFriends, $receiver, 2);
	$redis->zadd($receiverFriends, $doer, 1);
}


?>
