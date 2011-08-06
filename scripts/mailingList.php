<?php
include('lib/db.php');
$email = $_POST['email'];
mysql_query("INSERT INTO mailingList (email) values ('{$email}')");
header("Location: ../index.php");
?>