<?php

include ("mailer.php");
require_once 'lib/redisConfig.php';
$redis = new Predis\Client($single_server);
ob_start();
$host="localhost"; // Host name 
$username="rawr"; // Mysql username 
$password="rawrpass"; // Mysql password 
$db_name="rawrdb"; // Database name 
$tbl_name="users"; // Table name
// random string for confirmation

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

// Get the confirm code from the URL
$code=$_GET['code']; 

// To protect MySQL injection 
$code = stripslashes($code);
$code = mysql_real_escape_string($code);

//get the actual code for the user
$sql="SELECT * FROM ".$tbl_name." WHERE confirm='".$code."'";
$result=mysql_query($sql);

if(mysql_num_rows($result)>0){
	$SQLinfo = mysql_fetch_assoc($result);
	$email= $SQLinfo["email"];
	$password= $SQLinfo["password"];
	$user=$SQLinfo["alias"];
	$uid=$SQLinfo["uid"];
	$sql="UPDATE users SET confirm='yes' WHERE confirm='".$code."'";
	$redis->set("uid:".$uid.".alias",$user);
	$redis->set("uid:".$uid.".pic","0");
	mysql_query($sql);
	echo "nice! you're confirmed, go ahead and start posting.";
	$mailer->sendWelcome($user,$email,$password);
	
}
else {
	echo "this confirmation link doesn't exist =/ something went wrong.";
}
	


?>

