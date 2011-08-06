<?php

include ("mailer.php");

ob_start();
$host="localhost"; // Host name 
$username="rawr"; // Mysql username 
$password="rawrpass"; // Mysql password 
$db_name="rawrdb"; // Database name 
$tbl_name="users"; // Table name

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

// Define $myusername and $mypassword 
$email=$_GET['email']; 

// To protect MySQL injection 
$email = stripslashes($email);
$email = mysql_real_escape_string($email);
if(strpos($email,"@")==FALSE || strpos($email,".")==FALSE){
	echo "This is not a valid email";
}
else{

	$sql="SELECT password,alias FROM ".$tbl_name." WHERE email='".$email."'";
	$result=mysql_query($sql);
	$data = mysql_fetch_assoc($result);

	if(mysql_num_rows($result)>0) {
		$alias = $data["alias"];
		$pw = $data["password"];
	
		$mailer->sendNewPass($alias,$email,$pw);
		echo "Check your email!";
	}
	else{
		echo "Maybe I'm not looking hard enough, but I can't find this account. QQ.";
	}

}
?>

