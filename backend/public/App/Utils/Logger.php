<?php

namespace App\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

use App\Config\Config;

require dirname(__FILE__, 4) . '/vendor/autoload.php';

class Logger extends \Monolog\Logger
{

	private static $logger;

	/**
	 * インスタンスを生成
	 */
	private static function getInstance()
	{
		if (!isset(self::$logger)) {
			self::$logger = new Logger();
		}
		return self::$logger;
	}

	private function __construct($logfile = Config::DEFAULT_LOG_FILEPATH, $level = Config::DEFAULT_LOG_LEVEL, $channel = Config::DEFAULT_CHANNEL_NAME)
	{

		$format = Config::SIMPLE_FORMAT;
		$date_format = Config::SIMPLE_DATE_FORMAT;
		$formatter = new LineFormatter($format, $date_format, true);

		$stream = new StreamHandler($logfile, $level);
		$stream->setFormatter($formatter);

		$rotatingFile = new RotatingFileHandler($logfile);
		$rotatingFile->setFormatter($formatter);

		parent::__construct($channel);
		$this->pushHandler($stream);
		$this->pushHandler($rotatingFile);
	}

	/**
	 * ログメッセージの出力
	 *
	 * @param string $msg ログメッセージ
	 */
	public static function emergencyLog($msg)
	{
		self::getInstance()->emergency($msg);
	}

	public static function alertLog($msg)
	{
		self::getInstance()->alert($msg);
	}

	public static function criticalLog($msg)
	{
		self::getInstance()->critical($msg);
	}

	public static function errorLog($msg)
	{
		self::getInstance()->error($msg);
	}

	public static function warningLog($msg)
	{
		self::getInstance()->warning($msg);
	}

	public static function noticeLog($msg)
	{
		self::getInstance()->notice($msg);
	}

	public static function infoLog($msg)
	{
		self::getInstance()->info($msg);
	}

	public static function debugLog($msg)
	{
		self::getInstance()->debug($msg);
	}
}
