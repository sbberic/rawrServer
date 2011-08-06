<?php

include ("mailer.php");
ob_start();
$host="localhost"; // Host name 
$username="rawr"; // Mysql username 
$password="rawrpass"; // Mysql password 
$db_name="rawrdb"; // Database name 
$tbl_name="users"; // Table name
// random string for confirmation

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

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

// Define $myusername and $mypassword
$email=$_GET['email']; 
$pw=$_GET['password'];


// To protect MySQL injection 
$email = stripslashes($email);
$pw = stripslashes($pw);
$email = mysql_real_escape_string($email);
$pw = mysql_real_escape_string($pw);
$explodedAlias = explode("@",$email);
$alias = $explodedAlias[0];

if(strpos($email,"@")==FALSE || strpos($email,".")==FALSE || strlen($email) < 5){
	echo "This is not a valid email";
}
else if(strlen($pw)<5){echo "Please create a password longer than 5 characters";}
else {

	$sql="SELECT * FROM ".$tbl_name." WHERE email='".$email."'";
	$result=mysql_query($sql);

	if(mysql_num_rows($result)>0) {
		echo "This email is already exists, maybe you forgot your password?";
	}
	  	
   	else{
   		$confirmString=genConfirmRand(25);
   		$sql="INSERT INTO users (email, alias, password, type,confirm) VALUES ('".$email."','".$alias."','".$pw."',1,'".$confirmString."')";
   		mysql_query($sql);
   		$mailer->sendConfirm($alias,$confirmString,$email);
   		echo "yes";
	}
	
}
	


?>

