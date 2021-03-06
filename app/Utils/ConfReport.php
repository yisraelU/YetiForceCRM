<?php
/**
 * Conf report class.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace App\Utils;

use PDO;

/**
 * Conf report.
 */
class ConfReport
{
	/**
	 * Optional database configuration for offline use.
	 *
	 * @var array
	 */
	public static $dbConfig = [
		'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=yetiforce;',
		'user' => '',
		'password' => '',
		'options' => [],
	];
	/**
	 * List all variables.
	 *
	 * @var string[]
	 */
	public static $types = ['stability', 'security', 'libraries', 'database', 'performance'];
	/**
	 * List all container.
	 *
	 * @var string[]
	 */
	public static $container = ['php', 'env', 'ext', 'headers', 'db'];
	/**
	 * Php variables.
	 *
	 * @var mixed[]
	 */
	private static $php = [];
	/**
	 * Environment variables.
	 *
	 * @var mixed[]
	 */
	private static $env = [];
	/**
	 * Database variables.
	 *
	 * @var mixed[]
	 */
	private static $db = [];
	/**
	 * Extensions.
	 *
	 * @var mixed[]
	 */
	private static $ext = [];
	/**
	 * Request headers.
	 *
	 * @var mixed[]
	 */
	private static $headers = [];

	/**
	 * Sapi name.
	 *
	 * @var string
	 */
	private static $sapi = 'www';

	/**
	 * Stability variables map.
	 *
	 * @var array
	 */
	public static $stability = [
		'phpVersion' => ['recommended' => '7.1.x, 7.2.x (dev)', 'type' => 'Version', 'container' => 'env', 'testCli' => true, 'label' => 'PHP'],
		'error_reporting' => ['recommended' => 'E_ALL & ~E_NOTICE', 'type' => 'ErrorReporting', 'container' => 'php', 'testCli' => true],
		'output_buffering' => ['recommended' => 'On', 'type' => 'OnOffInt', 'container' => 'php', 'testCli' => true],
		'max_execution_time' => ['recommended' => 600, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'max_input_time' => ['recommended' => 600, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'default_socket_timeout' => ['recommended' => 600, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'memory_limit' => ['recommended' => '1 GB', 'type' => 'GreaterMb', 'container' => 'php', 'testCli' => true],
		'log_errors' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'file_uploads' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'short_open_tag' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'post_max_size' => ['recommended' => '50 MB', 'type' => 'GreaterMb', 'container' => 'php', 'testCli' => true],
		'upload_max_filesize' => ['recommended' => '100 MB', 'type' => 'GreaterMb', 'container' => 'php', 'testCli' => true],
		'max_input_vars' => ['recommended' => 10000, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'zlib.output_compression' => ['recommended' => 'Off', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session.auto_start' => ['recommended' => 'Off', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session.gc_maxlifetime' => ['recommended' => 21600, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'session.gc_divisor' => ['recommended' => 500, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'session.gc_probability' => ['recommended' => 1, 'type' => 'Equal', 'container' => 'php', 'testCli' => true],
		'mbstring.func_overload' => ['recommended' => 'Off', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true], //Roundcube
		'date.timezone' => ['type' => 'TimeZone', 'container' => 'php', 'testCli' => true], //Roundcube
		'allow_url_fopen' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true], //Roundcube
		'auto_detect_line_endings' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true], //CSVReader
	];
	/**
	 * Security variables map.
	 *
	 * @var array
	 */
	public static $security = [
		'HTTPS' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'env', 'testCli' => false],
		'public_html' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'env', 'testCli' => false],
		'display_errors' => ['recommended' => 'Off', 'type' => 'OnOff', 'container' => 'php', 'demoMode' => true, 'testCli' => true],
		'.htaccess' => ['recommended' => 'On', 'type' => 'Htaccess', 'container' => 'php', 'testCli' => false],
		'session.use_strict_mode' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session.use_trans_sid' => ['recommended' => 'Off', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session.cookie_httponly' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session.use_only_cookies' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session.cookie_secure' => ['recommended' => '?', 'type' => 'CookieSecure', 'container' => 'php', 'testCli' => true],
		'expose_php' => ['recommended' => 'Off', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'session_regenerate_id' => ['recommended' => 'On', 'type' => 'SessionRegenerate', 'testCli' => true],
		'Header: Server' => ['recommended' => '', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: X-Powered-By' => ['recommended' => '', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: X-Frame-Options' => ['recommended' => 'SAMEORIGIN', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: X-XSS-Protection' => ['recommended' => '1; mode=block', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: X-Content-Type-Options' => ['recommended' => 'nosniff', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: X-Robots-Tag' => ['recommended' => 'none', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: X-Permitted-Cross-Domain-Policies' => ['recommended' => 'none', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: Expect-CT' => ['recommended' => 'enforce; max-age=3600', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: Referrer-Policy' => ['recommended' => 'no-referrer', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
		'Header: Strict-Transport-Security' => ['recommended' => 'max-age=31536000; includeSubDomains; preload', 'type' => 'Header', 'container' => 'headers', 'testCli' => false],
	];
	/**
	 * Libraries map.
	 *
	 * @var array
	 */
	public static $libraries = [
		'imap' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'imap', 'container' => 'ext', 'testCli' => true],
		'pdo_mysql' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'pdo_mysql', 'container' => 'ext', 'testCli' => true],
		'mysqlnd' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'mysqlnd', 'container' => 'ext', 'testCli' => true],
		'openssl' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'openssl', 'container' => 'ext', 'testCli' => true],
		'curl' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'curl', 'container' => 'ext', 'testCli' => true],
		'gd' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'gd', 'container' => 'ext', 'testCli' => true],
		'pcre' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'pcre', 'container' => 'ext', 'testCli' => true],
		'xml' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'xml', 'container' => 'ext', 'testCli' => true],
		'json' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'json', 'container' => 'ext', 'testCli' => true],
		'session' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'session', 'container' => 'ext', 'testCli' => true],
		'dom' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'dom', 'container' => 'ext', 'testCli' => true],
		'zip' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'zip', 'container' => 'ext', 'testCli' => true],
		'mbstring' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'mbstring', 'container' => 'ext', 'testCli' => true],
		'soap' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'soap', 'container' => 'ext', 'testCli' => true],
		'fileinfo' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'fileinfo', 'container' => 'ext', 'testCli' => true],
		'iconv' => ['mandatory' => true, 'type' => 'ExtExist', 'extName' => 'iconv', 'container' => 'ext', 'testCli' => true],
		'exif' => ['mandatory' => false, 'type' => 'ExtExist', 'extName' => 'exif', 'container' => 'ext', 'testCli' => true],
		'ldap' => ['mandatory' => false, 'type' => 'ExtExist', 'extName' => 'ldap', 'container' => 'ext', 'testCli' => true],
		'OPcache' => ['mandatory' => false, 'type' => 'FnExist', 'fnName' => 'opcache_get_configuration', 'container' => 'ext', 'testCli' => true],
		'apcu' => ['mandatory' => false, 'type' => 'ExtExist', 'extName' => 'apcu', 'container' => 'ext', 'testCli' => true],
		'allExt' => ['container' => 'ext', 'type' => 'AllExt', 'testCli' => true, 'label' => 'EXTENSIONS'],
	];
	public static $directoryPermissions = [
	];
	/**
	 * Database map.
	 *
	 * @var array
	 */
	public static $database = [
		'driver' => ['recommended' => 'mysql', 'type' => 'Equal', 'container' => 'db', 'testCli' => false],
		'serverVersion' => ['container' => 'db', 'testCli' => false],
		'clientVersion' => ['container' => 'db', 'testCli' => false],
		'connectionStatus' => ['container' => 'db', 'testCli' => false],
		'serverInfo' => ['container' => 'db', 'testCli' => false],
		'innodb_lock_wait_timeout' => ['recommended' => 600, 'type' => 'Greater', 'container' => 'db', 'testCli' => false],
		'wait_timeout' => ['recommended' => 600, 'type' => 'Greater', 'container' => 'db', 'testCli' => false],
		'interactive_timeout' => ['recommended' => 600, 'type' => 'Greater', 'container' => 'db', 'testCli' => false],
		'sql_mode' => ['recommended' => '', 'type' => 'NotIn', 'container' => 'db', 'testCli' => false, 'values' => ['STRICT_ALL_TABLES', 'STRICT_TRANS_TABLE']],
		'max_allowed_packet' => ['recommended' => '10 MB', 'type' => 'GreaterMb', 'container' => 'db', 'testCli' => false],
		'log_error' => ['container' => 'db', 'testCli' => false],
		'max_connections' => ['container' => 'db', 'testCli' => false],
		'bulk_insert_buffer_size' => ['container' => 'db', 'testCli' => false],
		'key_buffer_size' => ['container' => 'db', 'testCli' => false],
		'thread_cache_size' => ['container' => 'db', 'testCli' => false],
		'query_cache_size' => ['container' => 'db', 'testCli' => false],
		'tmp_table_size' => ['container' => 'db', 'testCli' => false],
		'max_heap_table_size' => ['container' => 'db', 'testCli' => false],
		'innodb_file_per_table' => ['recommended' => 'On', 'container' => 'db', 'testCli' => false],
		'innodb_stats_on_metadata' => ['recommended' => 'Off', 'container' => 'db', 'testCli' => false],
		'innodb_buffer_pool_instances' => ['container' => 'db', 'testCli' => false],
		'innodb_buffer_pool_size' => ['container' => 'db', 'testCli' => false],
		'innodb_log_file_size' => ['container' => 'db', 'testCli' => false],
		'innodb_io_capacity_max' => ['container' => 'db', 'testCli' => false],
		'tx_isolation' => ['container' => 'db', 'testCli' => false],
		'transaction_isolation' => ['container' => 'db', 'testCli' => false],
		'character_set_server' => ['recommended' => 'utf8', 'type' => 'Equal', 'container' => 'db', 'testCli' => false],
		'character_set_database' => ['recommended' => 'utf8', 'type' => 'Equal', 'container' => 'db', 'testCli' => false],
		'character_set_client' => ['recommended' => 'utf8', 'type' => 'Equal', 'container' => 'db', 'testCli' => false],
		'character_set_connection' => ['recommended' => 'utf8', 'type' => 'Equal', 'container' => 'db', 'testCli' => false],
		'character_set_results' => ['recommended' => 'utf8', 'type' => 'Equal', 'container' => 'db', 'testCli' => false],
		'character_set_system' => ['container' => 'db', 'testCli' => false],
		'character_set_filesystem' => ['container' => 'db', 'testCli' => false],
	];
	/**
	 * Performance map.
	 *
	 * @var array
	 */
	public static $performance = [
		'xdebug' => ['recommended' => 'Off', 'type' => 'ExtNotExist', 'extName' => 'xdebug', 'container' => 'ext', 'testCli' => true],
		'opcache.enable' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'opcache.enable_cli' => ['recommended' => 'On', 'type' => 'OnOff', 'container' => 'php', 'testCli' => true],
		'opcache.max_accelerated_files' => ['recommended' => 40000, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'opcache.interned_strings_buffer' => ['recommended' => 100, 'type' => 'Greater', 'container' => 'php', 'testCli' => true],
		'opcache.validate_timestamps' => ['recommended' => 1, 'type' => 'Equal', 'container' => 'php', 'testCli' => true],
		'opcache.revalidate_freq' => ['recommended' => 30, 'type' => 'Equal', 'container' => 'php', 'testCli' => true],
		'opcache.save_comments' => ['recommended' => 0, 'type' => 'Equal', 'container' => 'php', 'testCli' => true],
		'opcache.memory_consumption' => ['container' => 'php', 'testCli' => true],
	];
	/**
	 * Environment map.
	 *
	 * @var array
	 */
	public static $environment = [
	];

	/**
	 * Get all configuration values.
	 *
	 * @return mixed
	 */
	public static function getAll()
	{
		static::init('all');
		$all = [];
		foreach (static::$types as $type) {
			$all[$type] = static::validate($type);
		}
		return $all;
	}

	/**
	 * Get configuration values by type of map.
	 *
	 * @param string $type
	 *
	 * @return mixed
	 */
	public static function get(string $type)
	{
		static::init($type);
		return static::validate($type);
	}

	/**
	 * Get configuration for cron.
	 *
	 * @return array
	 */
	public static function getForCron()
	{
		static::$sapi = 'cron';
		static::init('all');
		$all = [];
		foreach (static::$types as $type) {
			$all[$type] = static::parse($type);
		}
		return $all;
	}

	/**
	 * Validating configuration values.
	 *
	 * @param string $type
	 *
	 * @return mixed
	 */
	private static function validate(string $type)
	{
		$main = static::parse($type);
		$cron = static::getCronVariables($type);
		foreach (static::$$type as $key => &$item) {
			$item['status'] = true;
			if (isset($main[$key])) {
				$item[static::$sapi] = $main[$key];
			}
			if (isset($cron[$key])) {
				$item['cron'] = $cron[$key];
			}
			if (isset($item['type'])) {
				$methodName = 'validate' . $item['type'];
				if (\method_exists(__CLASS__, $methodName)) {
					$item = call_user_func_array([__CLASS__, $methodName], [$key, $item, 'www']);
					if ($item['testCli'] && !empty($cron)) {
						$item = call_user_func_array([__CLASS__, $methodName], [$key, $item, 'cron']);
					}
				}
				if (isset($item['skip'])) {
					unset(static::$$type[$key]);
				}
			}
		}
		return static::$$type;
	}

	/**
	 * Parser of configuration values.
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	private static function parse(string $type)
	{
		$values = [];
		foreach (static::$$type as $key => $item) {
			if (static::$sapi === 'cron' && !$item['testCli']) {
				continue;
			}
			if (isset($item['type']) && ($methodName = 'parser' . $item['type']) && \method_exists(__CLASS__, $methodName)) {
				$values[$key] = call_user_func_array([__CLASS__, $methodName], [$key, $item]);
			} elseif (isset($item['container'])) {
				$container = $item['container'];
				if (isset(static::$$container[\strtolower($key)]) || isset(static::$$container[$key])) {
					$values[$key] = static::$$container[\strtolower($key)] ?? static::$$container[$key];
				}
			}
		}
		return $values;
	}

	/**
	 * Initializing variables.
	 *
	 * @param string $type
	 */
	private static function init(string $type)
	{
		$types = static::$container;
		if (isset(static::$$type)) {
			$types = \array_unique(\array_column(static::$$type, 'container'));
		}
		$conf = static::getConfig();
		foreach ($types as $item) {
			switch ($item) {
				case 'php':
					static::$php = $conf['php'];
					break;
				case 'env':
					static::$env = $conf['env'];
					break;
				case 'ext':
					static::$ext = get_loaded_extensions();
					break;
				case 'headers':
					static::$headers = static::getRequestHeaders();
					break;
				case 'db':
					static::$db = static::getConfigDb();
					break;
			}
		}
	}

	/**
	 * Get variable for cron.
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	private static function getCronVariables(string $type)
	{
		if (file_exists('user_privileges/cron.php')) {
			$cron = include \ROOT_DIRECTORY . '/user_privileges/cron.php';
			return $cron[$type] ?? null;
		}
		return [];
	}

	/**
	 * Get environment variables.
	 *
	 * @return array
	 */
	private static function getConfig()
	{
		$php = [];
		foreach (ini_get_all() as $key => $value) {
			$php[$key] = $value['local_value'];
		}
		return [
			'php' => $php,
			'env' => [
				'phpVersion' => PHP_VERSION,
				'sapi' => PHP_SAPI,
				'phpIni' => php_ini_loaded_file(),
				'phpIniAll' => php_ini_scanned_files(),
				'https' => \App\RequestUtil::getBrowserInfo()->https,
				'public_html' => IS_PUBLIC_DIR ? 'On' : 'Off'
			]
		];
	}

	/**
	 * Get database variables.
	 *
	 * @return mixed[]
	 */
	private static function getConfigDb()
	{
		$pdo = false;
		if (\class_exists('\App\Db')) {
			$db = \App\Db::getInstance();
			$pdo = $db->getSlavePdo();
			$driver = $db->getDriverName();
		} elseif (!empty(static::$dbConfig['user'])) {
			$pdo = new PDO(static::$dbConfig['dsn'], static::$dbConfig['user'], static::$dbConfig['password'], static::$dbConfig['options']);
			$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
		}
		if (!$pdo) {
			return [];
		}
		$conf = [
			'driver' => $driver,
			'serverVersion' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
			'clientVersion' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
			'connectionStatus' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
			'serverInfo' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
		];
		$statement = $pdo->prepare('SHOW VARIABLES');
		$statement->execute();
		return \array_merge($conf, $statement->fetchAll(PDO::FETCH_KEY_PAIR));
	}

	/**
	 * Get request headers.
	 *
	 * @return array
	 */
	private static function getRequestHeaders()
	{
		$requestUrl = \AppConfig::main('site_URL');
		$headers = [];
		try {
			$res = (new \GuzzleHttp\Client())->request('GET', $requestUrl, ['timeout' => 1, 'verify' => false]);
			foreach ($res->getHeaders() as $key => $value) {
				$headers[strtolower($key)] = is_array($value) ? implode(',', $value) : $value;
			}
		} catch (\Throwable $e) {
		}
		return $headers;
	}

	/**
	 * Validate php version.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return mixed
	 */
	private static function validateVersion(string $name, array $row, string $sapi)
	{
		if (version_compare($row[$sapi], str_replace('x', 0, $row['recommended']), '<')) {
			$row['status'] = false;
		}
		return $row;
	}

	/**
	 * Validate error reporting.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateErrorReporting(string $name, array $row, string $sapi)
	{
		$current = $row[$sapi];
		$errorReporting = stripos($current, '_') === false ? \App\ErrorHandler::error2string($current) : $current;
		if ($row['recommended'] === 'E_ALL & ~E_NOTICE' && (E_ALL & ~E_NOTICE) === (int) $current) {
			$row[$sapi] = $row['recommended'];
		} else {
			$row['status'] = false;
			$row[$sapi] = implode(' | ', $errorReporting) . " ({$current})";
		}
		return $row;
	}

	/**
	 * Validate on, off and int values.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateOnOffInt(string $name, array $row, string $sapi)
	{
		if ($sapi !== 'cron' && strtolower($row[$sapi]) !== 'on') {
			$row['status'] = false;
		}
		return $row;
	}

	/**
	 * Validate number greater than recommended.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateGreater(string $name, array $row, string $sapi)
	{
		if ((int) $row[$sapi] > 0 && (int) $row[$sapi] < (int) $row['recommended']) {
			$row['status'] = false;
		}
		return $row;
	}

	/**
	 * Validate number in bytes greater than recommended.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateGreaterMb(string $name, array $row, string $sapi)
	{
		if ($row[$sapi] !== '-1' && \vtlib\Functions::parseBytes($row[$sapi]) < \vtlib\Functions::parseBytes($row['recommended'])) {
			$row['status'] = false;
		}
		$row[$sapi] = \vtlib\Functions::showBytes($row[$sapi]);
		return $row;
	}

	/**
	 * Validate equal value "recommended == current".
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateEqual(string $name, array $row, string $sapi)
	{
		if (strtolower((string) $row[$sapi]) !== strtolower((string) $row['recommended'])) {
			$row['status'] = false;
		}
		return $row;
	}

	/**
	 * Validate date timezone.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateTimeZone(string $name, array $row, string $sapi)
	{
		try {
			new \DateTimeZone($row[$sapi]);
		} catch (\Throwable $e) {
			$row[$sapi] = \App\Language::translate('LBL_INVALID_TIME_ZONE', 'Settings::ConfReport') . $row[$sapi];
			$row['status'] = false;
		}
		return $row;
	}

	/**
	 * Validate on or off value.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateOnOff(string $name, array $row, string $sapi)
	{
		if ($row[$sapi] !== $row['recommended'] && !(isset($row['demoMode']) && \AppConfig::main('systemMode') === 'demo')) {
			$row['status'] = false;
		}
		return $row;
	}

	/**
	 * Parser on or off value.
	 *
	 * @param string $name
	 * @param array  $row
	 *
	 * @return array
	 */
	private static function parserOnOff(string $name, array $row)
	{
		$container = $row['container'];
		$current = static::$$container[\strtolower($name)];
		static $map = ['on' => 'On', 'true' => 'On', 'off' => 'Off', 'false' => 'Off'];
		return isset($map[strtolower($current)]) ? $map[strtolower($current)] : ($current ? 'On' : 'Off');
	}

	/**
	 * Validate function exist.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateFnExist(string $name, array $row, string $sapi)
	{
		$status = function_exists($row['fnName']);
		if (!$status) {
			$row['status'] = false;
		}
		$row[$sapi] = $status ? 'LBL_YES' : 'LBL_NO';
		return $row;
	}

	/**
	 * Validate extension loaded.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateExtExist(string $name, array $row, string $sapi)
	{
		if (!\in_array($row['extName'], static::$ext)) {
			$row['status'] = false;
		}
		$row[$sapi] = $row['status'] ? 'LBL_YES' : 'LBL_NO';
		return $row;
	}

	/**
	 * Validate extension loaded.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateExtNotExist(string $name, array $row, string $sapi)
	{
		if (\in_array($row['extName'], static::$ext)) {
			$row['status'] = false;
		}
		$row[$sapi] = $row['status'] ? 'Off' : 'On';
		return $row;
	}

	/**
	 * Validate htaccess .
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateHtaccess(string $name, array $row, string $sapi)
	{
		if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') === false) {
			if (!isset($_SERVER['HTACCESS_TEST'])) {
				$row['status'] = false;
				$row[$sapi] = 'Off';
			} else {
				$row[$sapi] = 'On';
			}
		} else {
			$row['skip'] = true;
		}
		return $row;
	}

	/**
	 * Validate session.cookie_secure.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateCookieSecure(string $name, array $row, string $sapi)
	{
		$row[$sapi] = static::parserOnOff($name, $row);
		$row['recommended'] = static::$env['https'] ? 'On' : 'Off';
		$row['status'] = $row[$sapi] === $row['recommended'];
		return $row;
	}

	/**
	 * Validate session_regenerate_id.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateSessionRegenerate(string $name, array $row, string $sapi)
	{
		if (\AppConfig::main('site_URL')) {
			$row[$sapi] = \AppConfig::main('session_regenerate_id') ? 'On' : 'Off';
			$row['status'] = \AppConfig::main('session_regenerate_id');
		} else {
			$row['skip'] = true;
		}
		return $row;
	}

	/**
	 * Validate header.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateHeader(string $name, array $row, string $sapi)
	{
		$header = strtolower(\str_replace('Header: ', '', $name));
		if (isset(static::$headers[$header])) {
			$row['status'] = strtolower(static::$headers[$header]) === strtolower($row['recommended']);
			$row[$sapi] = static::$headers[$header];
		}
		return $row;
	}

	/**
	 * Validate not in array.
	 *
	 * @param string $name
	 * @param array  $row
	 * @param string $sapi
	 *
	 * @return array
	 */
	private static function validateNotIn(string $name, array $row, string $sapi)
	{
		$value = $row[$sapi];
		if (!\is_array($row[$sapi])) {
			$value = \explode(',', $row[$sapi]);
		}
		$recommended = (array) $row['values'];
		foreach ($recommended as $item) {
			if (\in_array($item, $value)) {
				$row['status'] = false;
				break;
			}
		}
		return $row;
	}

	/**
	 * Parser all extensions value.
	 *
	 * @param string $name
	 * @param array  $row
	 *
	 * @return array
	 */
	private static function parserAllExt(string $name, array $row)
	{
		return \implode(', ', static::$ext);
	}
}
