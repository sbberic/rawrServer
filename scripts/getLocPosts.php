<?php
require_once 'lib/redisConfig.php';

$lid=$_GET['lid'];
$start=$_GET['start'];
//$start="0";
$count=20;
$pc='0';




$postData = array();

$redis = new Predis\Client($single_server);


$posts = $redis->lrange('loc:'.$lid.'.posts', $start, $start+$count);

foreach ($posts as $pid){

	$postAuthor='post:'.$pid.'.author';
	$uid = trim($redis->get($postAuthor));
	
	$postType='post:'.$pid.'.type';
	$postText='post:'.$pid.'.text';
	$postScore='post:'.$pid.'.score';
	$postMedia='post:'.$pid.'.media';
	$postCrop='post:'.$pid.'.crop';
	$postTime='post:'.$pid.'.time';
	//$userPic='uid:'.$uid.'.pic';
	$userAlias='uid:'.$uid.'.alias';
	$postSpam='post:'.$pid.'.spam';
	
	$type = trim($redis->get($postType));
	$text = trim($redis->get($postText));
	$score = trim($redis->get($postScore));
	$media = trim($redis->get($postMedia));
	$time = trim($redis->get($postTime));
	//$pic = trim($redis->get($userPic));
	$alias= trim($redis->get($userAlias));
	$crop = trim($redis->get($postCrop));
	$spamCount = trim($redis->get($postSpam));
	
	if($spamCount >-1){
		$nextPost = array($pc => array(		'type' => $type,
											'author' => $uid,
											'text' => $text,
											//'pic' => $pic,
											'alias' => $alias,
											'media' => $media,
											'time' => $time,
											'score' => $score,
											'pid' => $pid
											));
		$postData = array_merge($postData, $nextPost);
		$pc++;
	}
}

$locPosts = json_encode($postData);
echo stripslashes($locPosts);


?>
