<?php
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
$myusername=$_GET['email']; 
$mypassword=$_GET['password'];


// To protect MySQL injection 
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);


	$sql="SELECT * FROM ".$tbl_name." WHERE email='".$myusername."'";
	$result=mysql_query($sql);
	
	
	if(mysql_num_rows($result)>0) {
		$userInfo = mysql_fetch_assoc($result);
		$DBpass = $userInfo["password"];
		$DBpass = stripslashes($DBpass);
   		$mypassword = stripslashes($mypassword);

		if(strcmp($mypassword,$DBpass)==0){
     		$json = array();
			$json['email'] = $userInfo['email'];
			$json['uid'] = $userInfo['uid'];
			$json['fid'] = $userInfo['fid'];
			$json['type'] = $userInfo['type'];
			$json['alias'] = $userInfo['alias'];
			$data[] = $json; //check if necessary
			
			echo json_encode($data);
   		}
   		else{
   			echo "no"; //wrong pass
   		}

	}
	  	
   	else{
   		echo "no"; //no username
   	}

?>

