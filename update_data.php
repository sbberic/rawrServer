<?php
session_start();
include('db.php');



require_once 'scripts/lib/redisConfig.php';
include("AWS/sdk-1.3.2/sdk-1.3.2/sdk.class.php");

$redis = new Predis\Client($single_server);



if(isSet($_POST['text']))
{
$s3 = new AmazonS3();
$bucket = 'rawr.ucberkeley';
$exists = $s3 -> if_bucket_exists($bucket);


$author=$_SESSION['uid']; 
$type= "text";
$text=$_POST['text']; 
$locName=$_POST['locName'];
$lid=$_SESSION['lid'];
$fileString = $_POST['fileName'];
$parent="UC_Berkeley";
$timestamp = date("D M j G:i:s +0000 Y");
$media = null;
$score = 0;

$pid=$redis->get('global.pid');
$redis->incr('global.pid');

$picExts = array(".jpg" => "image/jpeg", ".jpeg" => "image/jpeg",".gif" => "image/gif",".png" => "image/png",".tiff" => "image/tiff", ".tif" => "image/tiff");
$audExts = array(".mp3" => "audio/mpeg", ".wav" => "audio/wave", ".ogg" => "audio/ogg");
$appExts = array(".zip" => "application/zip");
$docExts = array(".pdf" => "application/pdf", ".txt" => "text/plain",".doc" => "application/msword",".docx" => "application/msword");
$targetPath = $_SERVER['DOCUMENT_ROOT'] . '/upload_temp/';
$file = "";
$fileExt = "";
$fileContent = "";
foreach($picExts as $ext => $contentType) {
	$tempPath = $targetPath.$fileString.$ext;
	if(file_exists($tempPath)) {
		$type = "pic";
		$file = $tempPath;
		$fileExt = $ext;
		$fileContent = $contentType;
		
	}
}

foreach($audExts as $ext => $contentType) {
	$tempPath = $targetPath.$fileString.$ext;
	if(file_exists($tempPath)) {
		$type = "audio";
		$file = $tempPath;
		$fileExt = $ext;
		$fileContent = $contentType;
		
	}
}

foreach($appExts as $ext => $contentType) {
	$tempPath = $targetPath.$fileString.$ext;
	if(file_exists($tempPath)) {
		$type = "app";
		$file = $tempPath;
		$fileExt = $ext;
		$fileContent = $contentType;
		
	}
}

foreach($docExts as $ext => $contentType) {
	$tempPath = $targetPath.$fileString.$ext;
	if(file_exists($tempPath)) {
		$type = "doc";
		$file = $tempPath;
		$fileExt = $ext;
		$fileContent = $contentType;
		
	}
}
if($file != "" AND $type != "doc"){
	$filename = $pid.$fileExt;
	if($exists) {
		$s3->batch()->create_object($bucket, $filename, array('fileUpload' => $file, 'content-type'=>$fileContent));
		$file_upload_response = $s3->batch()->send();
		if($file_upload_response->areOK()) {
			$s3->set_object_acl($bucket, $filename, AmazonS3::ACL_PUBLIC);
			$media= $s3->get_object_url($bucket, $filename) . PHP_EOL . PHP_EOL;
			unlink($file);
		}
	}
}

$postType='post:'.$pid.'.type';
$postAuthor='post:'.$pid.'.author';
$postText='post:'.$pid.'.text';
$postMedia='post:'.$pid.'.media';
$postScore='post:'.$pid.'.score';
$postTime ='post:'.$pid.'.time';
$postSpam ='post:'.$pid.'.spam';
$postLoc='post:'.$pid.'.loc';
$userAlias='uid:'.$author.'.alias';
$alias= trim($redis->get($userAlias));

$locPosts = 'loc:'.$lid.'.posts';
$parentPosts = 'loc:'.$parent.'.posts';
$userPosts = 'uid:'.$author.'.posts';

$newPost = array(
    $postType => $type,
    $postAuthor => $author,
    $postText => $text,
    $postMedia => $media,
    $postScore => $score,
    $postTime => $timestamp,
    $postSpam => 0,
    $postLoc => $locName
);
if(strcmp($parent, $lid)!=0){
	$redis->lpush($locPosts,$pid);
}
$redis->lpush($parentPosts,$pid);
$redis->lpush($userPosts,$pid);
$redis->mset($newPost);

/*
mysql_query("INSERT INTO messages (msg,lid,uid) values ('$content','$lid','$uid')");
$sql_in= mysql_query("SELECT msg,msg_id FROM messages order by msg_id desc");
$r=mysql_fetch_array($sql_in);
$msg=$r['msg'];
$msg_id=$r['msg_id'];
*/
}
?>
<?php
$display_string ="";
		$display_string .= "<li class='{$type}post' id='{$pid}li'>";
		$display_string .= "<div class='postContainer' id='{$pid}post'>";
		$display_string .= "<div class='postUserPic' align='left'>";
		$display_string .= "<img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$author}.png'>";
		$display_string .= "</div>";
		$display_string .= "<div class='postAuthor'>";
		$display_string .= "<b>{$alias}</b> <span style='font-size:10px; color:#777;'>@{$locName}</span>";
		$display_string .= "</div>";
		$display_string .= "<div class='postText'>";
		$display_string .= "<span>";
		$display_string .= $text;
		$display_string .= "</span></div>";
		$display_string .= "<div class='postScore' id='{$pid}Score'>";
		$display_string .= $score;
		$display_string .= "</div>";
		$display_string .= "<div class='postFav'><a href='#' id='{$pid}fav' onclick='fav({$pid},{$_SESSION['uid']}); return false;'>";
		$display_string .= "k";
		$display_string .= "</a></div>";
		$display_string .= "<div class='postReply'><a href='#' onclick='loadComments({$pid}); return false;'>";
		$display_string .= "q";
		$display_string .= "</a></div>";;
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
		echo $display_string;
?>		
		

