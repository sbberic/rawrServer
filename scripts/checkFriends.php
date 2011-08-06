<?php
include ("mailer.php");
include("../AWS/sdk-1.3.2/sdk-1.3.2/sdk.class.php");

$s3 = new AmazonS3();
$bucket = 'rawrimages';

$str = $_GET['str'];
$fid = $_GET['uid'];
$first = $_GET['first'];
$last = $_GET['last'];
$lat = $_GET['lat'];
$lon = $_GET['lon'];
$host="localhost"; // Host name 
$username="rawr"; // Mysql username 
$password="rawrpass"; // Mysql password 
$db_name="rawrdb"; // Database name 
$tbl_name="users"; // Table name

$redirect = "Location: ../home.php?lid=UC_Berkeley";
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

function genConfirmRand($size)
{
	$length = $size;
	$characters = "!0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWZYZ";
	$string = "";

	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, (strlen($characters)-1))];
	}

	return $string;
}

function sendAutoEmails($fids, $friendName) {
	$mfids = explode(" ", $fids);

	foreach($mfids as $fid) {
		$sql = "SELECT email,alias FROM ".$tbl_name." WHERE fid=".$fid;
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_assoc($result);
			$email = $row['email'];
			$user = $row['alias'];
			$mailer->sendAutoCreate($friendName, $user, $email);
			$sql = "UPDATE ".$tbl_name." SET confirm='yes' WHERE fid=".$fid;
			$result = mysql_query($sql);
		}
	}
}

$friends = explode(" ",$str);
$hasFriend = FALSE;
$nonConfirmed = "";
foreach($friends as $friend) {
	$friend = stripslashes($friend);
	$sql = "SELECT confirm FROM ".$tbl_name." WHERE fid=".$friend;
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	if($row['confirm'] == "yes") {
		$hasFriend = TRUE;
		
	}
	else {
		$nonConfirmed.=$friend." ";
	}


}

if($hasFriend) {
	session_start();
	
	$sql = "SELECT * FROM ".$tbl_name." WHERE fid=".$fid;
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$_SESSION["lid"] = "UC_Berkeley";
		$_SESSION["uid"] = $row['uid'];
		$_SESSION["fid"] = $fid;
		$_SESSION["email"] = $row['email'];
		$_SESSION["type"] = $row['type'];
		$_SESSION["alias"] = $row['alias'];
		header($redirect);
	}
	else {
		$email="none";
		$alias=$first." ".$last;
		$pw=genConfirmRand(25);
		$confirmString = "yes";
		$sql="INSERT INTO users (email, alias, password,fid, type,confirm) VALUES ('".$email."','".$alias."','".$pw."','".$fid."',1,'".$confirmString."')";
   		mysql_query($sql);
		sendAutoEmails($nonConfirmed, $alias);
		
		$file = "tempProfilePic.png";
		imagepng(imagecreatefromjpeg("http://graph.facebook.com/{$fid}/picture"),$file);
		
		//$file = $_FILES["file"]['tmp_name'];
		$filename = "{$uid}.png";
			$s3->batch()->create_object($bucket, $filename, array('fileUpload' => $file));
			$file_upload_response = $s3->batch()->send();
			if($file_upload_response->areOK()) {
				$s3->set_object_acl($bucket, $filename, AmazonS3::ACL_PUBLIC);
				$media= $s3->get_object_url($bucket, $filename) . PHP_EOL . PHP_EOL;
			}
		

		
		$sql2 = "SELECT * FROM ".$tbl_name." WHERE fid=".$fid;
		$result2 = mysql_query($sql2);
		$row = mysql_fetch_array($result2);
		$_SESSION["lid"] = "UC_Berkeley";
		$_SESSION["uid"] = $row['uid'];
		$_SESSION["fid"] = $fid;
		$_SESSION["type"] = $row['type'];
		$_SESSION["alias"] = $alias;
		header($redirect);

		
	}	

}
else {
	//redirect to php form
	$sql = "SELECT * FROM ".$tbl_name." WHERE fid=".$fid;
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0) {
		
	}
	else {
	}
}

?>