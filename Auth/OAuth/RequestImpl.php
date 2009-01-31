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


	public function __construct ( $uri = null, $method = null, $parameters = array(), $headers = array(), $body = null )
	{
		$uri = self::buildRequestURL($uri);
		$this->uri = $uri;

		if (empty($method)) $method = $_SERVER['REQUEST_METHOD'];
		$this->method = strtoupper($method);

		if (empty($headers)) $headers = self::getRequestHeaders();
		$this->headers = $headers;

		$this->body = $body;

		if (empty($parameters)) $parameters = $this->getRequestParameters();
		$this->parameters = $parameters;


	}


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
	 * Return the request method
	 *
	 * @return string
	 */
	public function getMethod ()
	{
		return $this->method;
	}


	/**
	 * Return the normalised url for signature checks
	 *
	 * @return string
	 */
	public function getRequestUrl ()
	{
		return $this->uri;
	}


	/**
	 * Get all request parameters
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
	 * @return array associative array of parameters
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
	 * Get a parameter, value is always urlencoded.
	 *
	 * @param string	parameter name
	 * @param boolean	urldecode	set to true to decode the value upon return
	 * @return string value		false when not found
	 */
	public function getParam ( $name, $urldecode )
	{
		if (array_key_exists($name, $this->parameters)) {
			$value = $this->parameters[$name];

			if ($urldecode) {
				// TODO
			}

			return $value;
		}

	}


	/**
	 * Set a parameter.
	 *
	 * @param string	parameter name
	 * @param string	parameter value
	 * @param boolean	encoded	set to true when the values are already encoded
	 */
	public function setParam ( $name, $value, $encoded )
	{
		if ($encoded) {
			// TODO
		}

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
	 * @param string OAuth version
	 */
	public function getVersion()
	{
		return $this->getParam('oauth_version', true);
	}


	/**
	 * Get the OAuth consumer key of the request.
	 *
	 * @param string OAuth consumer key
	 */
	public function getConsumerKey ()
	{
		return $this->getParam('oauth_consumer_key', true);
	}


	/**
	 * Get the OAuth signature method of the request.
	 *
	 * @param string OAuth signature method
	 */
	public function getSignatureMethod()
	{
		return $this->getParam('oauth_signature_method', true);
	}


	/**
	 * Get the OAuth signature of the request.
	 *
	 * @param string OAuth signature
	 */
	public function getSignature()
	{
		return $this->getParam('oauth_signature', true);
	}


	/**
	 * Get the OAuth timestamp of the request.
	 *
	 * @param string OAuth timestamp
	 */
	public function getTimestamp()
	{
		return $this->getParam('oauth_timestamp', true);
	}


	/**
	 * Get the OAuth nonce of the request.
	 *
	 * @param string OAuth nonce
	 */
	public function getNonce()
	{
		return $this->getParam('oauth_nonce', true);
	}


	/**
	 * Get the OAuth token of the request.
	 *
	 * @param string OAuth token
	 */
	public function getToken()
	{
		return $this->getParam('oauth_token', true);
	}


	/**
	 * Get the OAuth callback URL of the request.
	 *
	 * @param string OAuth callback URL
	 */
	public function getCallback()
	{
		return $this->getParam('oauth_callback', true);
	}


	/**
	 * Get the request headers.
	 *
	 * @return array associative array of request headers.
	 */
	private static function getRequestHeaders() {
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
	 * Get OAuth parameters for this request. Parameters are retrieved, in
	 * order of preference, from:
	 *
	 *   - Authorization header
	 *   - POST body
	 *   - GET query parameters
	 */
	private function getRequestParameters() {

		$parameters = array();

		if (!empty($_GET)) {
			$parameters = array_merge($parameters, $_GET);
		}

		if ($this->getMethod() == 'POST'  &&  $this->getRequestContentType() == 'application/x-www-form-urlencoded')
		{
			$parameters = array_merge($parameters, $_POST);
		}

		if (array_key_exists('Authorization', $this->headers))
		{
			$auth_header = trim($this->headers['Authorization']);

			if (strncasecmp($auth_header, 'OAuth ', 6) === 0)
			{
				$auth_parameters = self::splitHeader(substr($auth_header, 6));
				if (array_key_exists('realm', $auth_parameters)) {
					unset($auth_parameters['realm']);
				}

				$parameters = array_merge($parameters, $auth_parameters);
			}
		}

		return $parameters;
	}


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