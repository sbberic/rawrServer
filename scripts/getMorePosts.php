<?php
require_once 'lib/redisConfig.php';
$lid=$_GET['lid'];
$start=$_GET['start'];

$redis = new Predis\Client($single_server);

$posts = $redis->lrange('loc:'.$lid.'.posts', $start, $start+20);
$display_string = '';
foreach ($posts as $pid){

	$postAuthor='post:'.$pid.'.author';
	$uid = trim($redis->get($postAuthor));
	
	$postType='post:'.$pid.'.type';
	$postText='post:'.$pid.'.text';
	$postScore='post:'.$pid.'.likers';
	$postMedia='post:'.$pid.'.media';
	$postCrop='post:'.$pid.'.crop';
	$postTime='post:'.$pid.'.time';
	//$userPic='uid:'.$uid.'.pic';
	$userAlias='uid:'.$uid.'.alias';
	$postSpam='post:'.$pid.'.spam';
	
	$type = trim($redis->get($postType));
	$text = trim($redis->get($postText));
	$score = trim($redis->scard($postScore));
	$media = trim($redis->get($postMedia));
	$time = trim($redis->get($postTime));
	$likers = $redis->smembers($postScore);
	$alias= trim($redis->get($userAlias));
	$crop = trim($redis->get($postCrop));
	$spamCount = trim($redis->get($postSpam));
	
	if($spamCount >-1){
		$display_string .= "<li class='{$type}post'>";
		$display_string .= "<div class='postContainer'>";
		$display_string .= "<div class='postUserPic' align='left'>";
		$display_string .= "<img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$uid}.png'>";
		$display_string .= "</div>";
		$display_string .= "<div class='postAuthor'>";
		$display_string .= "<b>{$alias}</b>";
		$display_string .= "</div>";
		$display_string .= "<div class='postText'>";
		$display_string .= "<span>";
		$display_string .= $text;
		$display_string .= "</span></div>";
		$display_string .= "<div class='postScore' id='{$pid}Score'>";
		$display_string .= $score;
		$display_string .= "</div>";
		$display_string .= "<div class='postFavList' align='right'>";
		foreach ($likers as $likeUID){
			$display_string .= "<a href=''><img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$likeUID}.png' width='20' height='20'></a>";
		}
		$display_string .= "</div>";
		$display_string .= "<div class='postFav'><a href='#' id='{$pid}fav' onclick='fav({$pid},{$_SESSION['uid']}); return false;'>";
		$display_string .= "k";
		$display_string .= "</a></div>";
		if(strcmp($type,"pic")==0){
			$display_string .= "<div class='postType'>";
			$display_string .= "P";
			$display_string .= "</div>";
		
			$display_string .= "<div class='postMedia'>";
			$display_string .= "<a href='https://s3-us-west-1.amazonaws.com/rawr.ucberkeley/{$pid}.jpg'>";
			$display_string .= "<img src='https://s3-us-west-1.amazonaws.com/rawr.ucberkeley/{$pid}.jpg' width='70px' height='75px'>";
			$display_string .= "</a>";
			$display_string .= "</div>";
		
		}
		if(strcmp($type,"audio")==0){
			$display_string .= "<div class='postType'>";
			$display_string .= "m";
			$display_string .= "</div>";
			$display_string .= "<div class='postMedia'>";
			$display_string .= "<audio src='https://s3-us-west-1.amazonaws.com/rawr.ucberkeley/{$pid}.mp3' controls='controls' controls autobuffer></audio>";
			$display_string .= "</div>";
		}
		$display_string .= "</div>";
		$display_string .= "</li>";
		
	}
}
echo $display_string;

?>