<!doctype html> 
<html>
	<head>
		<title>rawr</title>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="http://cdn.simplegeo.com/js/1.3/simplegeo.storage.min.js"></script>
<link rel="stylesheet" href="css/index.css" type="text/css">

	</head>
    <body>
    <div id="container">
    	<div id="title">
    	rawr<span style="font-size:17px; color:gray;"><sub>alpha</sub> </span><span style="font-size:130px; color:black; font-style:georgia;">|</span>
    	</div>
    	<div id="subtitle">
    	<span style='font-family:Myriad; font-size:18px;'><span style="color:#A3DDA3;">rawr</span>  lets you share with groups of people around you based on <span style="background-color:#ABCDEF;">mutual events</span> or <span style="background-color:#AE83DC;">location</span>.  </span><Br><Br>
    	rawr is currently in alpha testing. Login through facebook below if you're on the list.
    	</div>
    	<div id="fb-root"></div>
      	<script src="http://connect.facebook.net/en_US/all.js"></script>
      	
<script>
			//Initiate facebook connect api
			FB.init({ 
				appId:'168796813156939', cookie:true, 
				status:true, xfbml:true 
			});
			
</script>
	
       <div id="fbbutton"><fb:login-button show-faces="true" width="500" max-rows="2">Login with Facebook</fb:login-button>
       </div>


	<script>
	function getLocs(lat,lon){
		var client = new simplegeo.StorageClient('7vtPWcyFYxeFsLasRxfUP5ExE4yeydzj');
		client.getNearby("com.calendade.ucberkeley", lat, lon, {"limit": 50, "radius": 2}, function(err, data) {
    		if (err) { 
        		console.error(err);
    		} else {
    			var locs = JSON.stringify(data);
				localStorage.setItem("locs", locs);
				localStorage.setItem("lid", data.features[0].properties.Parent)
	 		}

		});
	}
	
	var lon;
	var lat;
	
	if (navigator.geolocation) {
  		navigator.geolocation.getCurrentPosition(success,handle_errors, { enableHighAccuracy: true });  
    }  
    else {
  		error('not supported');
	}
  
        function handle_errors(error)  
        {  
            switch(error.code)  
            {  
                case error.PERMISSION_DENIED: alert("you have to share location data to use rawr :/");  
                break;  
  
                case error.POSITION_UNAVAILABLE: alert("could not detect current position");  
                break;  
  
                case error.TIMEOUT: alert("retrieving position timed out");  
                break;  
  
                default: alert("unknown error");  
                break;  
            }  
        }  
  
        
function success(position) {
	//	var client = new simplegeo.StorageClient('7vtPWcyFYxeFsLasRxfUP5ExE4yeydzj');
		lon= position.coords.longitude;
		lat= position.coords.latitude;
		localStorage.setItem("lat", lat);
		localStorage.setItem("lon", lon);
		localStorage.setItem("locName", "UC Berkeley");
		console.log('got to success');
		getLocs(lat,lon);
		
}

	
	//Check if User is logged in and authorized. 
			//if they are check against fb if they have friends who are current app users.
			FB.getLoginStatus(function(response) {
				if (response.session) {
					findFriends(response.session.uid);
				} else {
    					
				}
			});
			//Query fb db for friend users.
			var mUID;
			var mFirst;
			var mLast;
			
			function findFriends(user_id) {
				mUID = user_id;
				setName();
				FB.api(
  				{
    					method: 'fql.query',
    					query: 'SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = '+user_id+') AND is_app_user = 1'
  				},
  					callback
				);

				function callback(response) {
    					if(response) {
						var str = response[0].uid;
						for(i=1; i<response.length; i++) {
							str = str+"+"+response[i].uid;
						}
	
						
						//Probably want to encrypt this for future reference
						//window.location = './scripts/checkFriends.php?str=' + str + '&uid=' +mUID + '&last=' + mLast+ '&first=' + mFirst;
						window.location = './scripts/checkFriends.php?str=' + str + '&uid=' +mUID + '&last=' + mLast+ '&first=' + mFirst +'&lat=' + lat + '&lon='+ lon;
					}
					else {
						//where to redirect if user doesn't have friends in rawr
					}
  				}
				
			}
			function setName() {
				FB.api('/me', function(response) {
  					mFirst = response.first_name;
					mLast = response.last_name;
				});			
			}
			
	


      </script>
	<div id="info">
	</div><Br><Br><br>
	<div id="mailinglist">
	Want to use rawr?<br> Sign up for the mailing list and we'll send you an invite when we're ready.<br> 		
		<form id="mailingList">
		
			<input placeholder="email address" type="email" name="email"/>
			<input type="submit" name="submit" value="submit">

		</form>
		<script type="text/javascript">
$("#mailingList").submit(function() {//action="scripts/mailingList.php" method="post"
    $.ajax({
        type: "POST",
        url: "scripts/mailingList.php",
        data: $("#mailingList").serializeArray(),
        success: function() {
            alert("Thanks for signing up! We'll try to send you an invite ASAP.");
        }
        });
    return false;
});</script>
<Br>
		

	</div>
</div>
    </body>
 </html>