<?php
//this php script essentially force leaves the uid from lid 
//call on application kill or application goes into background
$fp = fsockopen("localhost", 800, $errno, $errstr, 30);
$uid = $_GET['uid'];
$lid = $_GET['lid'];
if (!$fp) {
    echo "$errstr ($errno)<br />\n"; //not sure what this is but i think its an error...
} 
else {
    $out = "left:";
    $out .= $uid.":";
    $out .= $lid;
    fwrite($fp, $out);
    fclose($fp);
}
echo $uid;
?>
