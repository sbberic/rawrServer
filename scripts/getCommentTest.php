<?php
require_once 'lib/redisConfig.php';

//Script to get comments of a post.
//Count limits number of posts retrieved.
$pid=$_GET['pid'];
// lets pull all comments at a time since it's all text and only text, will be easy to pull all at a time. 
// $start=$_GET['start'];
//$count=$_GET['count'];
$commentCount=0;
$commentData = array();

$redis = new Predis\Client($single_server);

$postCommentsKey = 'post:'.$pid.'comments';
$comments = $redis->lrange($postCommentsKey, $start, $start + 50); //temporary 50, make pull all posts

foreach ($comments as $cid) {
	$commentsCid = 'comment:'.$cid;

	$uidKey=$commentsCid.'.uid';
	$uid = trim($redis->get($uidKey));	
	$aliasKey='uid:'.$uid.'.alias';	
	$timeKey=$commentsCid.'.time';
	$textKey=$commentsCid.'.text';

	$alias = trim($redis->get($aliasKey));
	$time = trim($redis->get($timeKey));
	$text = trim($redis->get($textKey));

	$newComment = array($commentCount => array('author' => $uid,
  					           'text' => $text,
					           'alias' => $alias,
					           'time' => $time,
					           'pid' => $pid));
	$commentData = array_merge($commentData, $newComment);
	$commentCount++;
}
 
$postComments = json_encode($commentData);
echo stripslashes($postComments);
?> 

