<?php


require_once 'Auth/OAuth/Store.php';
require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/Store/ConsumerImpl.php';
require_once 'Auth/OAuth/Store/ServerImpl.php';
require_once 'Auth/OAuth/TokenImpl.php';

/**
 * An OAuth store implementation, backed by an SQLite database.
*/
class Auth_OAuth_Store_SQLite implements Auth_OAuth_Store
{

	/**
	 * SQLite database object
	 *
	 * @var SQLiteDatabase
	 */
	private $sqlite;


	/**
	 * Constructor.
	 *
	 * @param string $db filename of the SQLite database
	 * @param int $umask umask to set on SQLite database file
	 */
	public function __construct($file, $umask = 0666)
	{
		$this->sqlite = sqlite_factory($file, $umask, $error);

		if (!$this->sqlite) {
			error_log($error);
			return;
		}

		$this->build_tables();
	}


	/**
	 * Get an OAuth Consumer.
	 *
	 * @param string $consumer_key consumer key to get
	 * @return Auth_OAuth_Store_Consumer
	 */
	public function getConsumer ( $consumer_key ) 
	{
		$result = $this->sqlite->query('SELECT * FROM consumers WHERE key="' . $consumer_key . '"');

		if (!$result->valid()) {
			return false;
		}

		$data = $result->fetch();
		return new Auth_OAuth_Store_ConsumerImpl($data['key'], $data['secret']);
	}


	/**
	 * Update an OAuth Consumer.  If a consumer does not already exist with the 
	 * consumer_key, a new one will be added.
	 *
	 * @param Auth_OAuth_Store_Consumer $consumer consumer to update
	 */
	public function updateConsumer ( Auth_OAuth_Store_Consumer $consumer ) 
	{
		$sql = sprintf('REPLACE INTO consumers (key, secret) VALUES ("%s", "%s");', $consumer->getKey(), $consumer->getSecret());
		return $this->sqlite->queryExec($sql);
	}


	/**
	 * Delete an OAuth Consumer.
	 *
	 * @param string $consumer_key consumer key to delete
	 */
	public function deleteConsumer ( $consumer_key ) 
	{
		return $this->sqlite->queryExec('DELETE FROM consumers WHERE key="' . $consumer_key . '"');
	}


	/**
	 * Get an OAuth Consumer Token.
	 *
	 * @param string $token_key token to get
	 * @return Auth_OAuth_Token
	 */
	public function getConsumerToken ( $token_key ) 
	{
		$result = $this->sqlite->query('SELECT * FROM consumer_tokens WHERE token="' . $token_key . '"');

		if (!$result->valid()) {
			return false;
		}

		$data = $result->fetch();
		return new Auth_OAuth_TokenImpl($data['token'], $data['secret'], $data['consumer_key'], 
				$data['type'], $data['user'], (bool) $data['authorized']);
	}


	/**
	 * Get all OAuth Consumer Tokens issued to the specified user.
	 *
	 * @param int $user ID of user to get tokens for.  If null, tokens for all users will be retrieved.
	 * @return array of Auth_OAuth_Token objects
	 */
	public function getConsumerTokens ( $user = null ) 
	{
		$tokens = array();

		$sql = 'SELECT * FROM consumer_tokens';
		if ($user) $sql .= ' WHERE user="' . $user . '"';

		$result = $this->sqlite->query($sql);

		while ($result->valid()) {
			$data = $result->fetch();
			$tokens[] = new Auth_OAuth_TokenImpl($data['token'], $data['secret'], $data['consumer_key'], 
				$data['type'], $data['user'], (bool) $data['authorized']);
		}

		return $tokens;
	}


	/**
	 * Update an OAuth Consumer Token.  If a token does not already exist with the 
	 * token value, a new one will be added.
	 *
	 * @param Auth_OAuth_Token $token consumer token to add or update
	 */
	public function updateConsumerToken ( Auth_OAuth_Token $token )
	{
		$sql = sprintf('REPLACE INTO consumer_tokens (consumer_key, token, secret, type, user, authorized) VALUES ("%s", "%s", "%s", "%s", "%s", "%s");', 
			$token->getConsumerKey(), $token->getToken(), $token->getSecret(), $token->getType(), $token->getUser(), $token->isAuthorized());
		$this->sqlite->queryExec($sql);
	}


	/**
	 * Delete a consumer token.
	 *
	 * @param string $token_key token to be deleted
	 */
	public function deleteConsumerToken ( $token_key ) 
	{
		return $this->sqlite->queryExec('DELETE FROM consumer_tokens WHERE token="' . $token_key . '"');
	}


	/**
	 * Get an OAuth Server.
	 *
	 * @param string $consumer_key consumer key of server to get
	 * @return Auth_OAuth_Store_Server
	 */
	public function getServer ( $consumer_key ) 
	{
		$result = $this->sqlite->query('SELECT * FROM servers WHERE key="' . $consumer_key . '"');

		if (!$result->valid()) {
			return false;
		}

		$data = $result->fetch();

		$server = new Auth_OAuth_Store_ServerImpl($data['key'], $data['secret']);
		$server->setRequestTokenURI($data['request_url']);
		$server->setAuthorizeURI($data['authorize_url']);
		$server->setAccessTokenURI($data['access_url']);
		$server->setSignatureMethods(explode(';', $data['signature_methods']));

		return $server;
	}


	/**
	 * Update an OAuth Server.  If a server does not already exist with the 
	 * consumer_key, a new one will be added.
	 *
	 * @param Auth_OAuth_Store_Server $server server to update
	 */
	public function updateServer ( Auth_OAuth_Store_Server $server ) 
	{
		$sql = sprintf(
			'REPLACE INTO servers (key, secret, request_url, authorize_url, access_url, signature_methods) VALUES ("%s", "%s", "%s", "%s", "%s", "%s");', 
			$server->getKey(), $server->getSecret(), $server->getRequestTokenURI(), $server->getAuthorizeURI(), $server->getAccessTokenURI(), 
			implode(';', $server->getSignatureMethods())
		);
		return $this->sqlite->queryExec($sql);
	}


	/**
	 * Delete an OAuth Server.
	 *
	 * @param string $consumer_key consumer key to delete
	 */
	public function deleteServer ( $consumer_key ) 
	{
		return $this->sqlite->queryExec('DELETE FROM servers WHERE key="' . $consumer_key . '"');
	}


	/**
	 * Get an OAuth Server Token.
	 *
	 * @param string $token_key token to get
	 * @return Auth_OAuth_Token
	 */
	public function getServerToken ( $token_key ) 
	{
		$result = $this->sqlite->query('SELECT * FROM server_tokens WHERE token="' . $token_key . '"');

		if (!$result->valid()) {
			return false;
		}

		$data = $result->fetch();
		return new Auth_OAuth_TokenImpl($data['token'], $data['secret'], $data['consumer_key'], 
				$data['type'], $data['user']);
	}


	/**
	 * Get all OAuth Server Tokens issued to the specified user.
	 *
	 * @param int $user ID of user to get tokens for.  If null, tokens for all users will be retrieved.
	 * @return array of Auth_OAuth_Token objects
	 */
	public function getServerTokens ( $user = null ) 
	{
		$tokens = array();

		$sql = 'SELECT * FROM server_tokens';
		if ($user) $sql .= ' WHERE user="' . $user . '"';

		$result = $this->sqlite->query($sql);

		while ($result->valid()) {
			$data = $result->fetch();
			$tokens[] = new Auth_OAuth_TokenImpl($data['token'], $data['secret'], $data['consumer_key'], 
				$data['type'], $data['user']);
		}

		return $tokens;
	}


	/**
	 * Update an OAuth Server Token.  If a token does not already exist with the 
	 * token value, a new one will be added.
	 *
	 * @param Auth_OAuth_Token $token server token to add or update
	 */
	public function updateServerToken ( Auth_OAuth_Token $token ) 
	{
		$sql = sprintf('REPLACE INTO server_tokens (consumer_key, token, secret, type, user) VALUES ("%s", "%s", "%s", "%s", "%s");', 
			$token->getConsumerKey(), $token->getToken(), $token->getSecret(), $token->getType(), $token->getUser());
		$this->sqlite->queryExec($sql);
	}


	/**
	 * Delete a server token.
	 *
	 * @param string $token_key token to be deleted
	 */
	public function deleteServerToken ( $token_key ) 
	{
		return $this->sqlite->queryExec('DELETE FROM server_tokens WHERE token="' . $token_key . '"');
	}


	private function build_tables()
	{
		$sql = <<<EOT
		CREATE TABLE consumers (
			id INTEGER PRIMARY KEY,
			key TEXT UNIQUE,
			secret TEXT
		);

		CREATE TABLE consumer_tokens (
			id INTEGER PRIMARY KEY,
			consumer_key TEXT,
			token TEXT UNIQUE,
			secret TEXT,
			type TEXT,
			user INTEGER,
			authorized INTEGER
		);

		CREATE TABLE servers (
			id INTEGER PRIMARY KEY,
			key TEXT UNIQUE,
			secret TEXT,
			request_url TEXT,
			authorize_url TEXT,
			access_url TEXT,
			signature_methods TEXT
		);

		CREATE TABLE server_tokens (
			id INTEGER PRIMARY KEY,
			consumer_key TEXT,
			token TEXT UNIQUE,
			secret TEXT,
			type TEXT,
			user INTEGER
		);
EOT;

		$this->sqlite->queryExec($sql);
	}

	private static function escape() {
		$args = func_get_args();
		$escaped_args = array_map('sqlite_escape_string', array_slice($args, 1));
		array_unshift($escaped_args, $args[0]);

		return call_user_func_array('sprintf', $escaped_args);
	}

}


?>
