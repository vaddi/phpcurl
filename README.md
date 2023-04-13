# generic PHP curl Class #

A simple PHP curl Class to request http/s Domains.


## Features ##

- Send Formdata from PHP Array or String
- Send and Receive Cookie data from PHP Array or String
- Send Headerdata from PHP Array or String
- Set Timeout for request in Seconds


## Examples ##

First of all we will setup some Variables, will be easier to reuse later

	$url = 'http://127.0.0.1/phpcurl/debug.php';
	//$headers = array( 'Content-Type: text/html; charset=UTF-8', 'Content-Type: multipart/form-data; boundary=something' );
	$headers = array( 'Content-Type: application/x-www-form-urlencoded', 'Data: testing' );
	//$post_fields = 'showpw=0&challengev=null&myval=42'; // will also work
	$post_fields = array( 'showpw' => 0, 'challengev' => true, 'myval' => 42 );
	//$cookie = "eins=1;zwei=2;drei=3"; // will also work
	$cookie = array( 'eins' => 1, 'zwei' => 2, 'drei' => 3 );
	$timeout = 5; // Timeout in seconds



Create an Instance of the Class

	$CURL = new CURL();

full example

	$RESULT = $CURL->curl( $url, $headers, $post_fields, $cookie, $timeout );

whithout timeout, use default 5 seconds

	$RESULT = $CURL->curl( $url, $headers, $post_fields, $cookie );

also whithout any cookie data, default is false

	$RESULT = $CURL->curl( $url, $headers, $post_fields );

also whithout any post data (this results in a GET Request), default is null

	$RESULT = $CURL->curl( $url, $headers );

also whithout any header data, default is null

	$RESULT = $CURL->curl( $url );

same result as before, but with full writen arguments

	$RESULT = $CURL->curl( $url, null, null, false, 5 );

