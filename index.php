<?php

#[AllowDynamicProperties]
class CURL {

	private $_cookie			= null;
	private $_cookie_file	= null;
	private $_header			= null;
	private $_info				= null;

	/**
	 * function curl - do generic requests by curl
	 * @param string $query - URL to request, example: https://heise.de/
	 * @param array $headers - each headers data as array field, example: $headers = array( 'Content-Type: application/x-www-form-urlencoded' );
	 * @param array $post_fields - array of postdata 
	 * @param array $headers
	 * @param int $timeout (timeout in seconds, default = 5)
	 * @return string http response
	 */
	public function curl( $query = null, $headers = null, $post_fields = null, $cookie = false, $timeout = 5 ) {
	  if( $query === null ) return false;
	  try {
			if( function_exists( 'curl_exec' ) ) {
		    $cURLConnection = curl_init();
		    curl_setopt( $cURLConnection, CURLOPT_URL, $query );
		    if( $cookie && !empty( $cookie ) ) {
					$cookie = $this->transform( $cookie, ';', '=' );
					// ToDo: validate $cookie contains ";" and "=" as delimiters
					// get amount of "=" chars in $cookie, if there are more than one, also check for ";"
		      curl_setopt( $cURLConnection, CURLOPT_COOKIESESSION, true );
					curl_setopt( $cURLConnection, CURLOPT_COOKIE, $cookie );
		      curl_setopt( $cURLConnection, CURLOPT_COOKIEJAR, $this->_cookie ); // create cookie
		      curl_setopt( $cURLConnection, CURLOPT_COOKIEFILE, $this->get_cookie_file() ); // use cookie
		    }
		    if( $post_fields && !empty( $post_fields ) ) {
					$post_fields = $this->transform( $post_fields, '&', '=' );
					// ToDo: validate $$post_fields contains "&" and "=" as delimiters
					// get amount of "=" chars in $post_fields, if there are more than one, also check for "&"
		      curl_setopt( $cURLConnection, CURLOPT_POST, 1 );
		      curl_setopt( $cURLConnection, CURLOPT_POSTFIELDS, $post_fields );
		    }
		    if( $headers && !empty( $headers ) ) {
		      if( $post_fields && !empty( $post_fields ) ) { // if we have a POST request, add the content length to the header
		        $headers[] = 'Content-Length: ' . strlen( $post_fields );
		      }
		      curl_setopt( $cURLConnection, CURLOPT_HTTPHEADER, $headers );
		    }
		    curl_setopt( $cURLConnection, CURLOPT_FOLLOWLOCATION, true ); // Follow each Location Header?
		    curl_setopt( $cURLConnection, CURLOPT_AUTOREFERER, false ); // Set Referer Automaticly?
		    curl_setopt( $cURLConnection, CURLOPT_RETURNTRANSFER, true ); // true send data as string, false output them direct
		    curl_setopt( $cURLConnection, CURLOPT_HEADER, 1 ); // output also the response header
		    curl_setopt( $cURLConnection, CURLOPT_SSL_VERIFYHOST, false );
		    curl_setopt( $cURLConnection, CURLOPT_SSL_VERIFYPEER, false );
		    curl_setopt( $cURLConnection, CURLOPT_CONNECTTIMEOUT, $timeout );
		    curl_setopt( $cURLConnection, CURLOPT_VERBOSE, true ); // DEBUGGING
		    $data = curl_exec( $cURLConnection );
		    $header_size = curl_getinfo( $cURLConnection, CURLINFO_HEADER_SIZE );
		    $this->_header = substr( $data, 0, $header_size );
		    $body = substr( $data, $header_size );
				// get timing informations
				
				$this->_info = curl_getinfo( $cURLConnection );
		    // Matching the response to extract cookie value
		    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $data,  $match_found); 
		    $cookies = array(); 
		    foreach($match_found[1] as $item) { 
		        parse_str($item,  $cookie); 
		        $cookies = array_merge($cookies,  $cookie); 
		    }
		    $this->_cookie = $cookies;
		    //$this->_cookies = $match_found;
		    if( curl_errno( $cURLConnection ) ) {
		      throw new Exception( "Error: " . curl_error( $cURLConnection ) );
		      print "Error: " . curl_error( $cURLConnection );
		    }
		    curl_close( $cURLConnection );
			}
	    return $body;
	  } catch( Exception $e ) {
	    print_r( $e );
			exit();
	  }
	  return false;
	}


	/*
	 * Helper functions
	 */


	/**
	 * getCookies - returns the resonse cookie data
	 */
	public function getCookies() {
		return $this->_cookie;
	}
	
	/**
	 * getHeader - returns the response header data
	 */
	public function getHeader() {
		return $this->_header;
	}

	/**
	 * get_cookie_file - Helper function to get the cookie file path
	 * creates a file if the given not exists
	 */
	public function get_cookie_file() {
		if( $this->_cookie_file == null || $this->_cookie_file == "" ) {
			$cookieFile = "cookies.txt";
		} else {
			$cookieFile = $this->_cookie_file;
		}
		if(!file_exists($cookieFile)) {
		    $fh = fopen($cookieFile, "w");
		    fwrite($fh, "");
		    fclose($fh);
		}
		return $cookieFile;
	}

	/**
	 * transform - Helper function to transforms an array into a flatt string by variable delimiters
	 * @param array $$input 		- given array, must be flat, no nested elements
	 * @param $delimiter				- Delimiter between key and value pairs, default ","
	 * @param $value_delimiter	- Delimiter between key and value, default "="
	 */
	public function transform( $input = null, $delimiter = ',', $value_delimiter = '=' ) {
		if( is_array( $input ) ) {
			$fields_tmp = '';
			foreach( $input as $key => $value ) {
				$fields_tmp .= $delimiter;
				$fields_tmp .= $key . $value_delimiter . $value;
			}
			$input = $fields_tmp;
		}
		return $input;
	}

} // end class

//
// setup what we will send and to which url
//

define( 'PROTOCOL',	isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === "on" ? 'https://' : 'http://' );
define( 'HOST',			isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'localhost' );
define( 'PATH',			dirname($_SERVER['PHP_SELF']) );

$url = PROTOCOL . HOST . PATH . '/debug.php';
$headers = array( 'Content-Type: application/x-www-form-urlencoded', 'Data: testing' );
//$headers = array( 'Content-Type: text/html; charset=UTF-8', 'Content-Type: multipart/form-data; boundary=something' );
//$post_fields = 'showpw=0&challengev=null&myval=42'; // will also work
$post_fields = array( 'showpw' => 0, 'challengev' => true, 'myval' => 42 );
//$cookie = "eins=1;zwei=2;drei=3"; // will also work
$cookie = array( 'eins' => 1, 'zwei' => 2, 'drei' => 3 );
$timeout = 5; // Timeout in seconds

//
// Output
//

echo "<div>";
echo "Send a curl Request to: <a href='" . $url . "' target='_blank'>" . $url . "</a>\n";
echo "</div>\n";

echo "<pre>";
echo "\n";
echo "--- Request Data ---\n";
echo "\n";
echo "Request Header Data:\n";
print_r( $headers );
echo "\n";
echo "Post Data:\n";
print_r( $post_fields );
echo "\n";
echo "cookies:\n";
print_r( $cookie );
echo "</div>\n";

// create a new Instance of CURL Class
$CURL = new CURL();
$RESULT = $CURL->curl( $url, $headers, $post_fields, $cookie, $timeout ); // full example
//$RESULT = $CURL->curl( $url, $headers, $post_fields, $cookie ); // whithout timeout, use default 5 seconds
//$RESULT = $CURL->curl( $url, $headers, $post_fields ); // also whithout any cookie data, default is false
//$RESULT = $CURL->curl( $url, $headers ); // also whithout any post data (this results in a GET Request), default is null
//$RESULT = $CURL->curl( $url ); // also whithout any header data, default is null
//$RESULT = $CURL->curl( $url, null, null, false, 5 ); // same result, full writen arguments (optional)

echo "<pre style='background:#eee;'>--- Response Data ---\n";
echo $RESULT . "\n";
echo "</pre>";

echo "<pre>";
echo "Server Cookie Data:\n";
print_r( $CURL->getCookies() );
echo "\n";
echo "Response Header:\n";
print_r( $CURL->getHeader() );
echo "</pre>\n";

?>
