<?php
//this php script essentially force leaves the uid from lid 
//call on applicationcomes back from background or inactivity
$fp = fsockopen("localhost", 800, $errno, $errstr, 30);
$uid = $_GET['uid'];
$lid = $_GET['lid'];
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} 
else {
    $out = "join:";
    $out .= $uid.":";
    $out .= $lid;
    fwrite($fp, $out);
    fclose($fp);
}
echo $uid;
?>
