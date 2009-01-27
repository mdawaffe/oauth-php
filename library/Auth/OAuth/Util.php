<?php

class Auth_OAuth_Util
{

	/**
	 * Encode a string according to the RFC3986
	 * 
	 * @param string s
	 * @return string
	 */
	public static function urlencode ( $s ) { }
	

	/**
	 * Decode a string according to RFC3986.
	 * Also correctly decodes RFC1738 urls.
	 * 
	 * @param string s
	 * @return string
	 */
	public static function urldecode ( $s ) { }


	/**
	 * urltranscode - make sure that a value is encoded using RFC3986.
	 * We use a basic urldecode() function so that any use of '+' as the
	 * encoding of the space character is correctly handled.
	 * 
	 * @param string s
	 * @return string
	 */
	public static function urltranscode ( $s ) { }


	/**
	 * Simple function to perform a redirect (GET).
	 * Redirects the User-Agent, does not return.
	 * 
	 * @param string uri
	 * @param array params		parameters, urlencoded
	 * @exception OAuthException when redirect uri is illegal
	 */
	public static function redirect ( $uri, $params ) { }


	/**
	 * Parse the uri into its parts.  Fill in the missing parts.
	 * 
	 * @todo  check for the use of https, right now we default to http
	 * @todo  support for multiple occurences of parameters
	 * @param string $parameters  optional extra parameters (from eg the http post)
	 */
	public static function parseUri ( $parameters ) { }


	/**
	 * Return the default port for a scheme
	 * 
	 * @param string scheme
	 * @return int
	 */
	public static function defaultPortForScheme ( $scheme ) { }

	public static function sendResponse( $parameters ) 
	{
		$response = '';

		foreach ($parameters as $name => $value) 
		{
			if (!empty($response)) {
				$response .= '&';
			}

			$response .= $name . '=' . self::urlencode($value);
		}

		header('HTTP/1.1 200 OK');
		header('Content-Length: ' . strlen($result));
		header('Content-Type: application/x-www-form-urlencoded');

		echo $result;
		exit;
	}

}

?>
