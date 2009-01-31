<?php

require_once 'Auth/OAuth/Request.php';

interface Auth_OAuth_Server
{

	/**
	 * Handle request for a new Request Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function requestToken ( Auth_OAuth_Request $request );


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function authorizeStart ( Auth_OAuth_Request $request );


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function authorizeFinish ( Auth_OAuth_Request $request );


	/**
	 * Handle request for a new Access Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function accessToken ( Auth_OAuth_Request $request );

}

?>
