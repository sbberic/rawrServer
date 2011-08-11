<?php 
session_start(); 
if(!session_is_registered('uid') || !session_is_registered('alias')){
	header("Location: index.php");
}
$_SESSION["lid"] = $_GET['lid'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>rawr</title>
<script type="text/javascript" src="http://cdn.simplegeo.com/js/1.3/simplegeo.context.min.js"></script>
<link href="/scripts/lib/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/scripts/lib/uploadify/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/scripts/lib/uploadify/swfobject.js"></script>
<script type="text/javascript" src="/scripts/lib/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<link rel="stylesheet" href="pictosfont/pictos.css" type="text/css" charset="utf-8">
<link rel="stylesheet" href="css/home.css" type="text/css">

<script type="text/javascript">
var tempFileName;

$(function() {

$(".comment_button").click(function() 
{

	
    var element = $(this);
   
    var boxval = $("#content").val();
	
	var fileName;
	if(tempFileName!=null) { 
		fileName = tempFileName;
	}
	var locationName=localStorage.getItem("locName");
	var dataString = 'text=' + boxval + '&fileName=' +fileName+'&locName='+locationName;
	
	if(boxval=='')
	{
		alert("Please Enter Some Text");
	}
	else
	{
	$("#flash").show();
	$("#flash").fadeIn(400).html('<img src="img/ajax.gif" align="absmiddle">&nbsp;<span class="loading">Loading Update...</span>');
$.ajax({
		type: "POST",
  url: "update_data.php",
  data: dataString,
  cache: false,
  success: function(html){
 
  $("ol#update").prepend(html);
  $("ol#update li:first").slideDown("slow");
   document.getElementById('content').value='';
  $("#flash").hide();
	
  }
 });
}
return false;
	});

$(".comment_submit").click(function() 
{ 
 
	console.log("clicked");
    var elementID = this.id;
	var pid = elementID.substring(0,indexOf("submit"));
    var boxval = $("#comment_content").val();
	
	var locationName=localStorage.getItem("locName");
	var dataString = 'commentText=' + boxval +'&locName='+locationName + '&pid=' + pid;
	
	if(boxval=='')
	{
		alert("Please Enter Some Text");
	}
	else
	{
	$("#flash").show();
	$("#flash").fadeIn(400).html('<img src="img/ajax.gif" align="absmiddle">&nbsp;<span class="loading">Submitting Comment...</span>');
$.ajax({
		type: "POST",
  url: "update_data.php",
  data: dataString,
  cache: false,
  success: function(html){
 
  $("ol#"+pid+"commentsOL").append(html);
  $("ol#"+pid+"commentsOL li:last").slideDown("slow");
   document.getElementById(pid+'cc').value='';
  $("#flash").hide();
	
  }
 });
}
return false;
	});
});

function randomString(length)
{
  chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  pass = "";
  for(x=0;x<length;x++)
  {
    i = Math.floor(Math.random() * 62);
    pass += chars.charAt(i);
  }
  return pass;
}

function setRandFileName() 
{
  tempFileName=randomString(10);
}

function fav(pid,uid){
	//$(div#pid).style.color="#E3170D";
	document.getElementById(pid+'fav').style.color="#E3170D";
	$.post('scripts/favPost.php', {uid: uid, pid: pid }, function(data) {
  		$('div#'+pid+'Score').html(data);
	});
	return false;
}

function loadComments(pid){ 
	//$("li#"+pid+"li").addClass("expand");
	if(document.getElementById(pid+"comments") == null) {
		$.post('scripts/getMoreComments.php', {pid: pid }, function(data) {
			$("li#"+pid+"li").append(data);
			$("div#"+pid+"comments").slideDown("slow");
			console.log(pid);
			
		});
	}
	else if(document.getElementById(pid+"comments").style.display == "none"){
		$("div#"+pid+"comments").slideDown();
		//$("li#"+pid+"li").addClass("expand");
	}
	else{
		
		$("div#"+pid+"comments").slideUp();
		//$("li#"+pid+"li").removeClass("expand");
	}
	return false;
}

function filterPicture(){ 
      $(".text"+"post").slideUp(); 
	  $(".audio"+"post").slideUp(); 
	  $(".pic"+"post").slideDown();
	  return false;
}


function filterMusic(){ 
      $(".text"+"post").slideUp(); 
	  $(".pic"+"post").slideUp(); 
	  $(".audio"+"post").slideDown();
	  return false;
}

function filterText(){ 
      $(".audio"+"post").slideUp(); 
	  $(".pic"+"post").slideUp(); 
	  $(".text"+"post").slideDown();
	  return false;
}


var start =31;
var done = false;
$(document).ready(function(){

$('.menuButton').click(function() {
		var name = this.id;
		localStorage.setItem("locName", name);
		console.log('i was here at store');
});


var newURL = window.location.href.split('=');
var storedLid = newURL[1];
localStorage.setItem("lid", storedLid);

$("#updat li").hover( 
function () {
   $(this).addClass("hover");
},
function () {
   $(this).removeClass("hover");
}
)
$('.postContainer').click(function(){
	var pid = this.id;
	pid = pid.substring(0,pid.indexOf('post'));
	loadComments(pid);
});

$("postUserPic").load(function() {
    $(this).wrap(function(){
      return '<span class="' + $(this).attr('class') + '" style="background:url(' + $(this).attr('src') + ') no-repeat center center; width: ' + $(this).width() + 'px; height: ' + $(this).height() + 'px;" />';
    });
    $(this).css("opacity","0");
  });
  
   var fetching = false;
	
   function lastPostFunc()
   {
   	$('div#loader').html('<img src="img/ajax2.gif">'); //sets the loader
      fetching = true;
      $.post("scripts/getMorePosts.php?lid="+storedLid+"&start="+start, 

      function(data){
         if (data != "") {
           $(data).appendTo('ol#updat');
           start = start+ 20;
           
           
           $(".postContainer").hover(
 			 function () {
 		   $(this).addClass("hover");
 		 	},
 		 	function () {
  		  	$(this).removeClass("hover");
  			}
			);
           $('div#loader').empty();
           setTimeout(function(){
             fetching = false;
           },500);
         }
         else{
         	done = true;
         	$('div#loader').empty();
         }
     });
  };

  $(window).scroll(function(){
  	
    	var bufferzone = $(window).scrollTop() * 0.20;
    	if  (!fetching && ($(window).scrollTop() + bufferzone > ($(document).height()- $(window).height() ) )){
    	if(done !=true){
    lastPostFunc();
  }}
});



});
	
</script>
</head>

<body>

  <div id="title" style="color: #999;">
   <a href="home.php?lid=UC_Berkeley" class='menuButton' id='UC Berkeley'><span style='color:#B4EEB4;'>UC Berkeley:</span> <span style='font-size: 18px;'><script>document.write(localStorage.getItem("locName"));</script></span></a>
  <span style="font-family: 'Pictos', sans-serif;">
  <a href="#" onclick="filterMusic(); return false;"><li>m</li></a>
  <a href="#" onclick="filterPicture(); return false;"><li>P</li></a>
  <a href="#" onclick="filterText(); return false;"><li>w</li></a>
  <a href=""><li>s</li></a>
  </span>
  </div>
  
  <div id="subLocs">
  <div id="menu">
  <ul id="menuList">
  <script>
  
  	
  	var locs = localStorage.getItem("locs");
  		locs = JSON.parse(locs);
  		locs = locs.features;
  	for(var i=0; i<locs.length;i++) {
  		var lid = locs[i].id;
  		var name = locs[i].properties.Name;
  		console.log(name);
  		$('#menuList').append("<li><a href='home.php?lid="+lid+"' class='menuButton' id='"+name+"'>"+name+"</a></li>");
	}
  </script>
  </ul>
  </div>
  </div>

<div align="center">
<table cellpadding="0" cellspacing="0" width="500px">
<tr>
<td>

	
<div align="left">
<form method="post" enctype="multipart/form-data" name="form" action="" id ="mform">
<table cellpadding="0" cellspacing="0" width="500px">

<tr><td align="left"><div align="left"><h3>Care to share?</h3></div></td></tr>
<tr>
<td>
<script>
$(function() {
  $('#file_upload').uploadify({
    'uploader'      : '/scripts/lib/uploadify/uploadify.swf',
    'script'        : '/scripts/lib/uploadify/uploadify.php',
    'cancelImg'     : '/scripts/lib/uploadify/cancel.png',
	'fileDataName' : 'Filedata',
	'auto'		   : true,
	'onSelectOnce' : function(event,data) {
      setRandFileName();
	  var json = {'tempFile':tempFileName};
	  $('#file_upload').uploadifySettings('scriptData',json);
	  var button = document.getElementById('v');
	  button.disabled = true;
    },
	'onComplete' : function (event, ID, fileObj, response, data) {
	  var button = document.getElementById('v');
	  button.disabled = false;
	},
	'onCancel' : function (event,ID,fileObj,data) {
	  var button = document.getElementById('v');
	  button.disabled = false;
	}
  });
});
</script>
<input id="file_upload" name="file_upload" type="file"/>
</td>
</tr>
<tr>
<td style="padding:4px; padding-left:10px;" class="comment_box">
<span style="font-family:'Pictos'; float:left; font-size:44px;">W</span>
<textarea cols="30" rows="2" style="margin: 2px 0px 5px 5px;float: left;width:430px;font-size:14px; font-weight:bold" name="content" id="content" maxlength="145" ></textarea><br />
<input type="submit" value="Update"  id="v" name="submit" class="comment_button"/>

</td>

</tr>

</table>
</form>


</div>
<div style="height:7px"></div>
<div id="flash" align="left"  ></div>
<ol id="update" class="oldtimeline">

</ol>


<div id="old_updates">
<ol id="updat" class="oldtimeline">

<?php
require_once 'scripts/lib/redisConfig.php';
$lid=$_GET['lid'];

$redis = new Predis\Client($single_server);

$posts = $redis->lrange('loc:'.$lid.'.posts', 0, 30);
if(!$posts){echo "nothing posted here yet...";}
$display_string = '';
foreach ($posts as $pid){

	$postAuthor='post:'.$pid.'.author';
	$uid = trim($redis->get($postAuthor));
	
	$postType='post:'.$pid.'.type';
	$postText='post:'.$pid.'.text';
	$postScore='post:'.$pid.'.likers';
	$postMedia='post:'.$pid.'.media';
	$postCrop='post:'.$pid.'.crop';
	$postTime='post:'.$pid.'.time';
	//$userPic='uid:'.$uid.'.pic';
	$userAlias='uid:'.$uid.'.alias';
	$postSpam='post:'.$pid.'.spam';
	$postLoc='post:'.$pid.'.loc';
	
	$type = trim($redis->get($postType));
	$text = trim($redis->get($postText));
	$score = trim($redis->scard($postScore));
	$media = trim($redis->get($postMedia));
	$time = trim($redis->get($postTime));
	$likers = $redis->smembers($postScore);
	$alias= trim($redis->get($userAlias));
	$crop = trim($redis->get($postCrop));
	$spamCount = trim($redis->get($postSpam));
	$loc=trim($redis->get($postLoc));
	/*
	<script type="text/javascript" src='http://www.scribd.com/javascripts/view.js'></script>

	<div id='scribd_embedded' >
	<a href='http://www.scribd.com'>Scribd</a>
	</div>

	<script type="text/javascript">
	//retrieve doc_id and access_key from redis
	var doc_id;
	var access_key;
	var scribd_doc = scribd.Document.getDoc(doc_id, access_key);

	var oniPaperReady = function(e){
	
	}

	scribd_doc.addEventListener( 'iPaperReady', oniPaperReady );
	scribd_doc.addParam('height', 600);
	scribd_doc.addParam('width', 400);
	//scribd_doc.addParam('page', 10);
	scribd_doc.addParam('public', true);

	scribd_doc.write('scribd_embedded');
	</script>
	*/
	
	if($spamCount >-1){
		$display_string .= "<li class='{$type}post' id='{$pid}li' style='display: block;'>";
		$display_string .= "<div class='postContainer' id='{$pid}post'>";
		$display_string .= "<div class='postUserPic' align='left'>";
		$display_string .= "<img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$uid}.png'>";
		/*this line reads user pic from server
		$display_string .= "<img src='img/user_imgs/{$uid}.png'>";
		*/
		$display_string .= "</div>";
		$display_string .= "<div class='postAuthor'>";
		$display_string .= "<b>{$alias}</b> <span style='font-size:10px; color:#777;'>@{$loc}</span>";
		$display_string .= "</div>";
		$display_string .= "<div class='postText'>";
		$display_string .= "<span>";
		$display_string .= $text;
		$display_string .= "</span></div>";
		$display_string .= "</div>";
		$display_string .= "<div class='postScore' id='{$pid}Score'>";
		$display_string .= $score;
		$display_string .= "</div>";
		$display_string .= "<div class='postFavList' align='right'>";
		foreach ($likers as $likeUID){
			$display_string .= "<a href=''><img src='https://s3-us-west-1.amazonaws.com/rawrimages/{$likeUID}.png' width='20' height='20'></a>";
		}
		$display_string .= "</div>";
		$display_string .= "<div class='postFav'><a href='#' id='{$pid}fav' onclick='fav({$pid},{$_SESSION['uid']}); return false;'>";
		$display_string .= "k";
		$display_string .= "</a></div>";
		$display_string .= "<div class='postReply'><a href='#' onclick='loadComments({$pid}); return false;'>";
		$display_string .= "q";
		$display_string .= "</a></div>";;
		if(strcmp($type,"pic")==0){
		
			$display_string .= "<div class='postMedia'>";
			$display_string .= "<a href='https://s3-us-west-1.amazonaws.com/rawr.ucberkeley/{$pid}.jpg'>";
			$display_string .= "<img src='https://s3-us-west-1.amazonaws.com/rawr.ucberkeley/{$pid}.jpg' width='70px' height='75px'>";
			$display_string .= "</a>";
			$display_string .= "</div>";
		
		}
		if(strcmp($type,"audio")==0){
			$display_string .= "<div class='postMedia'>";
			$display_string .= "<audio src='https://s3-us-west-1.amazonaws.com/rawr.ucberkeley/{$pid}.mp3' controls='controls' controls autobuffer></audio>";
			$display_string .= "</div>";
		}
		
		$display_string .= "</li>";
		
		

		
	}}
echo $display_string;

?>
</div>

</td>
</tr>
</table>
<div id="loader"></div>

</div>
</body>
</html>
