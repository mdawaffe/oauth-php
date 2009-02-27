<?php

/**
 * OAuth Utility functions.
 */
class Auth_OAuth_Util
{

	/**
	 * Encode a string according to the RFC3986
	 *
	 * @param string $s string to encode
	 * @return string encoded string
	 */
	public static function encode ( $s )
	{
		if ($s !== false) {
			$s = str_replace('%7E', '~', rawurlencode($s));
		}

		return $s;
	}


	/**
	 * Decode a string according to RFC3986.
	 * Also correctly decodes RFC1738 urls.
	 *
	 * @param string $s string to decode
	 * @return string decoded string
	 */
	public static function decode ( $s )
	{
		if ($s !== false) {
			$s = rawurldecode($s);
		}

		return $s;
	}


	/**
	 * urltranscode - make sure that a value is encoded using RFC3986.
	 * We use a basic urldecode() function so that any use of '+' as the
	 * encoding of the space character is correctly handled.
	 *
	 * @param string $s string to transcode
	 * @return string
	 */
	public static function urltranscode ( &$s )
	{
		if ($s !== false) {
			$s = self::encode(urldecode($s));
		}

		return $s;
	}


	/**
	 * Get the HTTP request headers.  Header names have been normalized, stripping
	 * the leading 'HTTP_' if present, and capitalizing only the first letter
	 * of each word.
	 *
	 * @return array associative array of request headers.
	 */
	public static function getRequestHeaders() {
		if (function_exists('apache_request_headers')) {
			// We need this to get the actual Authorization:
			// header because apache tends to tell us it doesn't exist.
			return apache_request_headers();
		}

		// If we're not using apache, we just have to hope that _SERVER actually
		// contains what we need.
		$headers = array();

		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == 'HTTP_') {
				// this is chaos, basically it is just there to capitalize the first
				// letter of every word that is not an initial HTTP and strip HTTP
				// code from przemek
				$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
				$headers[$key] = $value;
			}
		}
		return $headers;
	}


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
	 * Return the default port for a URI scheme.
	 *
	 * @param string $scheme URI scheme
	 * @return int
	 */
	public static function defaultPortForScheme ( $scheme )
	{
		switch (strtolower($scheme))
		{
			case 'http':    return 80;
			case 'https':   return 443;
			default:        return null;
		}
	}


	public static function sendResponse( $parameters )
	{
		$response = '';

		foreach ($parameters as $name => $value)
		{
			$parameters[$name] = $name . '=' . self::encode($value);
		}

		header('HTTP/1.1 200 OK');
		header('Content-Length: ' . strlen($result));
		header('Content-Type: application/x-www-form-urlencoded');

		echo $result;
		exit;
	}

    /**
     * Generate a unique key
     *
     * @param boolean unique    force the key to be unique
     * @return string
     */
    public function generateKey ( $unique = false )
    {
        $key = md5(uniqid(rand(), true));
        if ($unique)
        {
            list($usec,$sec) = explode(' ',microtime());
            $key .= dechex($usec).dechex($sec);
        }
        return $key;
    }

}

?>
