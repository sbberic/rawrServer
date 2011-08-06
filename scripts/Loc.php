<?php
require_once('oauth.php');

//SimpleGeo Key and Secret
$CONSUMER_KEY = 'kY8zdDYZwtpx6Hhj2KBJqngqVWYW3YX2';
$CONSUMER_SECRET = 'b6dtXwaQDyW7eHkACGec38sCPc3D2sdm';

//Get layer - will modify this later to have the layer as a parentloc variable through GET


//Get lat and long values from ?lat=xxxx&lon=xxxx
$lat = $_GET['lat'];
$lon = $_GET['lon'];
//$parent = $_GET['parent'];
$layer='com.calendade.ucberkeley';


//These are the HTTP parameters that get sent as part of the HTTP request to AuthGrid
$params = array();

//This is where you can set the message payload if you need to send content for the API call you are making
//If you need to send a payload, here is an example that is commented out
//The example shown here can be used to update a user's first name and last name
//$payload = <<<PAYLOAD
//<user>
// <username>testuser</username>
// <attribute type="String" name="First name">
// <value>Test</value>
// </attribute>
// <attribute type="String" name="Last name">
// <value>User</value>
// </attribute>
//</user>
//PAYLOAD;
$payload = NULL;//If there is no payload required, set it to NULL

//The HTTP method to use for your request
$http_method = 'GET';

//------------------------------------------------

//YOU DO NOT NEED TO MODIFY ANYTHING BELOW

//Establish an OAuth consumer based on your client credentials
$consumer = new OAuthConsumer($CONSUMER_KEY, $CONSUMER_SECRET, NULL);


//Request URL for simplegeo api by nearest through layer/Lat/Long variables
$request_url = 'http://api.simplegeo.com/0.1/records/'.$layer.'/nearby/'.$lat.','.$lon.'.json?radius=2&limit=50';

//Setup the OAuth request
$request = OAuthRequest::from_consumer_and_token($consumer, NULL, $http_method, $request_url, $params);

// Sign the constructed OAuth request using HMAC-SHA1
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);

// Make signed OAuth request
$url = $request_url;// . '?' . format_params('=', '&', $params);
echo send_request($request->get_normalized_http_method(), $url, $request->to_header(), $payload);
 
/**
 * Makes an HTTP request to the specified URL
 * @param string $http_method The HTTP method (GET, POST, PUT, DELETE)
 * @param string $url Full URL of the resource to access
 * @param string $auth_header (optional) Authorization header
 * @param string $postData (optional) POST/PUT request body
 * @return string Response body from the server
 */
function send_request($http_method, $url, $auth_header=null, $postData=null) {
 $curl = curl_init($url);
 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($curl, CURLOPT_FAILONERROR, false);
 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

 switch($http_method) {
 case 'GET':
 if ($auth_header) {
 curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header)); 
 }
 break;
 case 'POST':
 curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 
 $auth_header)); 
 curl_setopt($curl, CURLOPT_POST, 1);                                       
 curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
 break;
 case 'PUT':
 curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 
 $auth_header)); 
 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
 curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
 break;
 case 'DELETE':
 curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header)); 
 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method); 
 break;
 }
 $response = curl_exec($curl);
 if (!$response) {
 $response = curl_error($curl);
 }
 curl_close($curl);
 return $response;
}

function format_params($inner_glue, $outer_glue, $array) {
 $output = array();
 foreach($array as $key => $item) {
 $output[] = $key . $inner_glue . urlencode($item);
 }
 return implode($outer_glue, $output);
}
?>