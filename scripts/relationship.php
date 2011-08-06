<?php
require_once 'lib/redisConfig.php';

$doer=$_POST['uid1']; 
$receiver=$_POST['uid2'];

$redis = new Predis\Client($single_server);

$doerFriends ='uid:'.$doer.'.friends';
$relationship = $redis->zscore($doerFriends, $receiver);

return $relationship; 
?>
