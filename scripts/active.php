<?php //no use for this script at the moment
require_once 'lib/redisConfig.php';
$redis = new Predis\Client($single_server);

$lid=$_POST['lid'];
$uid=$_POST['uid'];
$oldlid=$_GET['from'];
$active='loc:'.$lid.'.active';
$oldactive='loc:'.$oldlid.'.active';
if($oldlid == '0'){
	$redis->sadd($active,$uid);
	echo "active";
}
else{
	$redis->smove($oldactive, $active, $uid);
	echo "moved";
}


?>
