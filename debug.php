<?php

$result = array();

// set a cookie value
setcookie('request', 'ok', time()+ 1800, "/");

// get request method
$result['request_method'] = isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] != null ? $_SERVER['REQUEST_METHOD'] : null;

// get content type from request header
$result['request_content_type'] = isset( $_SERVER["CONTENT_TYPE"] ) && $_SERVER["CONTENT_TYPE"] != null ? $_SERVER["CONTENT_TYPE"] : null;

// get formdata 
$requestData = $_REQUEST;
$request = [];
foreach( $requestData as $key => $value ) {
  $request[ $key ] = $value;
}
$result['request_formdata'] = $request;

// get cookie values from request
$result['request_cookie'] = $_COOKIE;

//
// output
//

echo "<pre>";
foreach( $result as $key => $value ) {
	echo $key . ": ";
	print_r( $value );
	echo "\n";
}
echo "</pre>";

?>
