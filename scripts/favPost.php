<?php
require_once 'lib/redisConfig.php';

$uid=$_POST['uid']; 
$pid=$_POST['pid'];

$redis = new Predis\Client($single_server);

$likedPosts='uid:'.$uid.'.likes';
$postLikers='post:'.$pid.'.likers';
$postAuthor='post:'.$pid.'.author';
$authorScore='uid:'.$postAuthor.'.score';

$redis->sadd($likedPosts,$pid);
$redis->sadd($postLikers,$uid);
$score = $redis->scard($postLikers);
$redis->incr($authorScore);
echo $score;
?>
