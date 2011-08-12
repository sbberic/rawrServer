<?php
require_once 'lib/redisConfig.php';

$pid=$_POST['pid'];
$redis = new Predis\Client($single_server);

$postCommentsKey = 'post:'.$pid.'comments';
$comments = $redis->lrange($postCommentsKey, 0, 50); //temporary 50, make pull all posts
$display_string = "<div class='postComments' id='{$pid}comments'><ol style='list-style:none;'id={$pid}commentsOL>";
foreach ($comments as $cid) {
	$commentsCid = 'comment:'.$cid;

	$uidKey=$commentsCid.'.uid';
	$uid = trim($redis->get($uidKey));	
	$aliasKey='uid:'.$uid.'.alias';
	$spamKey=$commentsCid.'.spam';
	$timeKey=$commentsCid.'.time';
	$textKey=$commentsCid.'.text';
	
	
	$alias = trim($redis->get($aliasKey));
	$time = trim($redis->get($timeKey));
	$text = trim($redis->get($textKey));
	$spamCount = trim($redis->get($spamKey));

	
	
	if($spamCount >-1){
		$display_string .= "<li class='{$cid}comment'>";
		$display_string .= "<div class='commentContainer'>";
		/* 	comment user pic
		$display_string .= "<div class='commentUserPic' align='left'>";
		$display_string .= "<img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$uid}.png'>";
		$display_string .= "</div>";
		*/
		$display_string .= "<div class='commentAuthor'>";
		$display_string .= "<b>{$alias}</b>";
		$display_string .= "</div>";
		$display_string .= "<div class='commentText'>";
		$display_string .= "<span>";
		$display_string .= $text;
		$display_string .= "</span></div>";
		/* 	comment score
		$display_string .= "<div class='postScore' id='{$pid}Score'>";
		$display_string .= $score;
		$display_string .= "</div>";
		/*	comment fav list
		$display_string .= "<div class='postFavList' align='right'>";
		foreach ($likers as $likeUID){
			$display_string .= "<a href=''><img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$likeUID}.png' width='20' height='20'></a>";
		}	
		$display_string .= "</div>";
		/*	fav star
		$display_string .= "<div class='postFav'><a href='#' id='{$pid}fav' onclick='fav({$pid},{$_SESSION['uid']}); return false;'>";
		$display_string .= "k";
		$display_string .= "</a></div>";
		*/
		
		$display_string .= "</div>";
		$display_string .= "</li>";
		
	}
}		
		$display_string .= "</ol>";
		$display_string .= "<div class='newComment'>";
		$display_string .= "<textarea cols='30' rows='2' style='margin: 2px 0px 5px 5px;float: left;width:430px;font-size:14px; font-weight:bold' name='comment_content' id='{$pid}cc' maxlength='145' ></textarea><br><br>
";
		$display_string .= "<button value='Submit' id={$pid}submit class='comment_submit' onClick='commentClick({$pid})'/>";
		$display_string .= "</div></div>";
		
echo stripslashes($display_string);
?> 
