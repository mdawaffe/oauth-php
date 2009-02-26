<?php

require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/Util.php';

/**
 * Object to parse an incoming OAuth request or prepare an outgoing OAuth request
 */
class Auth_OAuth_RequestImpl implements Auth_OAuth_Request
{

	private $method;

	private $headers;

	private $uri;

	// private $uri_parts;

	private $parameters;

	private $realm;


	/**
	 * Construct a new Auth_OAuth_RequestImpl object.  All parameters are optional, and if absent,
	 * will be populated based on the current HTTP request using the $_SERVER global variable.
	 *
	 * @param string $uri URI this request is for
	 * @param string $method HTTP method used for this request
	 * @param array $parameters associative array of OAuth parameters for this request
	 * @param array $headers associative array of request headers.  Headers should be normalized, removing 
	 *                       any 'HTTP_' prefix and capitalizing only the first letter of each word.
	 * @param string $body request body
	 */
	public function __construct ( $uri = null, $method = null, $parameters = array(), $headers = array(), $body = null )
	{
		$uri = self::buildRequestURL($uri);
		$this->uri = $uri;

		if (empty($method)) $method = $_SERVER['REQUEST_METHOD'];
		$this->method = strtoupper($method);

		if (empty($headers)) $headers = Auth_OAuth_Util::getRequestHeaders();
		$this->headers = $headers;

		$this->body = $body;

		if (empty($parameters)) {
			$parameters = array();
			$request_parameters = $this->getRequestParameters();

			// we must decode the raw request parameters
			foreach ($request_parameters as $key => $value) {
				$key = Auth_OAuth_Util::decode($key);

				if (is_array($value)) {
					$values = array();
					foreach ($value as $v) {
						$values[] = Auth_OAuth_Util::decode($v);
					}
					$value = $values;
				} else {
					$value = Auth_OAuth_Util::decode($value);
				}

				$parameters[$key] = $value;
			}
		}

		$this->parameters = $parameters;
	}


	/**
	 * Build the request URL.  The provided url is used, and any missing components of the URL or populated
	 * from the current HTTP request using the $_SERVER global variable.
	 *
	 * @param string $url URL used to build request URL
	 * @return string complete request URL
	 */
	private static function buildRequestURL ( $url = null )
	{
		if (!empty($url)) {
			extract(parse_url($url));
		}

		if (empty($scheme)) $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
		$scheme = strtolower($scheme);

		if (empty($host)) $host = $_SERVER['HTTP_HOST'];
		$host = strtolower($host);

		if (empty($port)) $port = $_SERVER['SERVER_PORT'];
		if ($port != Auth_OAuth_Util::defaultPortForScheme($scheme)) {
			$host .= ':' . $port;
		}

		if (empty($path)) $path = $_SERVER['REQUEST_URI'];
		if (strpos($path, '?') !== false) list($path, $query) = explode('?', $path, 2);

		return $scheme . '://' . $host . $path;
	}


	/**
	 * Get the request method.
	 *
	 * @return string
	 */
	public function getMethod ()
	{
		return $this->method;
	}


	/**
	 * Get the normalised url for signature checks.
	 *
	 * @return string
	 */
	public function getRequestUrl ()
	{
		return $this->uri;
	}


	/**
	 * Get all request parameters.
	 *
	 * @return array associative array of parameters
	 */
	public function getParameters ()
	{
		return $this->parameters;
	}


	/**
	 * Return the complete parameter string for the signature check.
	 * All parameters are correctly urlencoded and sorted on name and value
	 *
	 * @return string normalized parameter string
	 */
	public function getNormalizedParameterString ()
	{
		$parameters = $this->getParameters();
		$normalized = array();

		ksort($parameters);
		foreach ($parameters as $key => $value) {
			if ($key == 'oauth_signature') continue;

			$key = Auth_OAuth_Util::encode($key);

			if (is_array($value)) {
				sort($value, SORT_STRING);
				foreach ($value as $v) {
					$v = Auth_OAuth_Util::encode($v);
					$normalized[] = $key . '=' . $v;
				}
			} else {
				$value = Auth_OAuth_Util::encode($value);
				$normalized[] = $key . '=' . $value;
			}
		}

		return implode('&', $normalized);
	}


	/**
	 * Get a parameter. Return value is NOT url encoded.
	 *
	 * @param string $name parameter name
	 * @return string|boolean parameter value, or false when not found
	 */
	public function getParam ( $name )
	{
		if (array_key_exists($name, $this->parameters)) {
			return $this->parameters[$name];
		}

	}


	/**
	 * Set a parameter.  Value should NOT be url encoded.
	 *
	 * @param string	parameter name
	 * @param string	parameter value
	 */
	public function setParam ( $name, $value )
	{
		$this->parameters[$name] = $value;
	}


	/**
	 * Return the body of the OAuth request.
	 *
	 * @return string	null when no body
	 */
	public function getBody ()
	{

	}


	/**
	 * Set the body of the OAuth request.
	 *
	 * @param string	null when no body
	 */
	public function setBody ( $body )
	{
	}


	/**
	 * Get the OAuth version of the request.
	 *
	 * @return string OAuth version
	 */
	public function getVersion()
	{
		return $this->getParam('oauth_version');
	}


	/**
	 * Get the OAuth consumer key of the request.
	 *
	 * @return string OAuth consumer key
	 */
	public function getConsumerKey ()
	{
		return $this->getParam('oauth_consumer_key');
	}


	/**
	 * Get the OAuth signature method of the request.
	 *
	 * @return string OAuth signature method
	 */
	public function getSignatureMethod()
	{
		return $this->getParam('oauth_signature_method');
	}


	/**
	 * Get the OAuth signature of the request.
	 *
	 * @return string OAuth signature
	 */
	public function getSignature()
	{
		return $this->getParam('oauth_signature');
	}


	/**
	 * Get the OAuth timestamp of the request.
	 *
	 * @return string OAuth timestamp
	 */
	public function getTimestamp()
	{
		return $this->getParam('oauth_timestamp');
	}


	/**
	 * Get the OAuth nonce of the request.
	 *
	 * @return string OAuth nonce
	 */
	public function getNonce()
	{
		return $this->getParam('oauth_nonce');
	}


	/**
	 * Get the OAuth token of the request.
	 *
	 * @return string OAuth token
	 */
	public function getToken()
	{
		return $this->getParam('oauth_token');
	}


	/**
	 * Get the OAuth callback URL of the request.
	 *
	 * @return string OAuth callback URL
	 */
	public function getCallback()
	{
		return $this->getParam('oauth_callback');
	}


	/**
	 * Get the OAuth authorization realm.  This will only ever be populated if the
	 * request used an HTTP Authorization header to pass the OAuth parameters.
	 *
	 * @return string OAuth authorization realm
	 */
	public function getRealm()
	{
		return $this->realm;
	}


	/**
	 * Get OAuth parameters for this request. Parameters are retrieved, in
	 * order of preference, from:
	 *
	 *   - Authorization header
	 *   - POST body
	 *   - GET query parameters
	 *
	 * Parameter values are returned exactly as they appear in the request, and
	 * have not been urldecoded.
	 *
	 * When parsing the Authorization header, if an authorization realm is present,
	 * it is set on the current object.  Otherwise, this method does not modify the
	 * current request object in any way.
	 *
	 * @return array associative array of request parameters.
	 */
	private function getRequestParameters() {

		$parameters = array();

		// start with GET parameters
		if (!empty($_GET)) {
			$parameters = array_merge($parameters, $_GET);
		}

		// allow POST parameters to override
		if ($this->getMethod() == 'POST'  &&  $this->getRequestContentType() == 'application/x-www-form-urlencoded')
		{
			$parameters = array_merge($parameters, $_POST);
		}

		// allow Authorization header parameters to override
		if (array_key_exists('Authorization', $this->headers))
		{
			$auth_header = trim($this->headers['Authorization']);

			if (strncasecmp($auth_header, 'OAuth ', 6) === 0)
			{
				$auth_parameters = self::splitHeader(substr($auth_header, 6));
				if (array_key_exists('realm', $auth_parameters)) {
					$this->realm = $auth_parameters['realm'];
					unset($auth_parameters['realm']);
				}

				$parameters = array_merge($parameters, $auth_parameters);
			}
		}

		return $parameters;
	}


	/**
	 * Split the OAuth authorization header into individual key/value pairs.
	 *
	 * @param string $header Authorization header value, with the authorization scheme removed
	 * @return array associative array of authorization header values
	 */
	private static function splitHeader($header) {
		$parameters = array();

		$vs = explode(',', $header);
		foreach ($vs as $v)
		{
			if (strpos($v, '=') !== false)
			{
				$v = trim($v);
				list($name, $value) = explode('=', $v, 2);
				if (!empty($value) && $value{0} == '"' && substr($value, -1) == '"')
				{
					$value = substr($value, 1, -1);
				}

				$parameters[$name] = $value;
			}
		}

		return $parameters;
	}


    /**
     * Fetch the content type of the current request
     *
     * @return string
     */
    private function getRequestContentType ()
    {
        $content_type = 'application/octet-stream';
        if (!empty($this->headers) && array_key_exists('Content-Type', $this->headers))
        {
            list($content_type) = explode(';', $this->headers['Content-Type']);
        }
        return trim($content_type);
    }


    /**
     * Get the body of a POST or PUT.
     *
     * Used for fetching the post parameters and to calculate the body signature.
     *
     * @return string       null when no body present (or wrong content type for body)
     */
    private function getRequestBody ()
    {
        $body = null;
        if ($this->method == 'POST' || $this->method == 'PUT')
        {
            $body = '';
            $fh   = @fopen('php://input', 'r');
            if ($fh)
            {
                while (!feof($fh))
                {
                    $s = fread($fh, 1024);
                    if (is_string($s))
                    {
                        $body .= $s;
                    }
                }
                fclose($fh);
            }
        }
        return $body;
    }

}

/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>