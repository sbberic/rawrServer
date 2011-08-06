<?php
require_once 'lib/redisConfig.php';

$author=$_GET['uid']; 
$text=$_GET['text']; 
$pid=$_GET['pid'];
$timestamp = $_GET['timestamp'];

$redis = new Predis\Client($single_server);
$cid = $redis->get('global.cid');

$commentAuthor='comment:'.$cid.'.uid';
$commentText='comment:'.$cid.'.text';
$commentTime='comment:'.$cid.'.time';

$newComment = array(
	$commentAuthor=> $author,
	$commentText => $text,
	$commentTime => $timestamp
);

$postComments = 'post:'.$pid.'comments';

//appending comment to post's list of cids
$redis->rpush($postComments, $cid);

$redis->mset($newComment);
$redis->incr('global.cid');

echo "comment stored";
?>
