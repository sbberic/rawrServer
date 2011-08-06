<?php
require_once 'lib/redisConfig.php';
include("../AWS/sdk-1.3.2/sdk-1.3.2/sdk.class.php");

$s3 = new AmazonS3();
$bucket = 'rawr.ucberkeley';
$exists = $s3 -> if_bucket_exists($bucket);


$author=$_POST['uid']; 
$type=$_POST['type'];
$text=$_POST['text']; 
$lid=$_POST['lid'];
$locName=$_POST['locName'];
$parent=$_POST['parent'];
$timestamp = $_POST['timestamp'];
$media = null;
$score = 0;

$redis = new Predis\Client($single_server);
$pid = $redis->get('global.pid');

if(strcmp($type,"pic")==0){
	$file = $_FILES["file"]['tmp_name'];
	$filename = $pid.".jpg";
	if($exists) {
		$s3->batch()->create_object($bucket, $filename, array('fileUpload' => $file));
		$file_upload_response = $s3->batch()->send();
		if($file_upload_response->areOK()) {
			$s3->set_object_acl($bucket, $filename, AmazonS3::ACL_PUBLIC);
			$media= $s3->get_object_url($bucket, $filename) . PHP_EOL . PHP_EOL;
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
$redis->incr('global.pid');

echo "ok";
?>
