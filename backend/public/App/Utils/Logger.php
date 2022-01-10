<?php

declare(strict_types=1);

namespace App\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

use App\Config\Config;

require dirname(__FILE__, 4) . '/vendor/autoload.php';

/**
 * ロギング
 */
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
     * ログレベル:emergency
     * システムが使用不可能な状態
     *
     * @param string $msg ログメッセージ
     */
    public static function emergencyLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->emergency($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:alert
     * 直ちになんらかの対処の必要がある
     *
     * @param string $msg ログメッセージ
     */
    public static function alertLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->alert($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:critical
     * 危機的な状態
     *
     * @param string $msg ログメッセージ
     */
    public static function criticalLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->critical($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:error
     * 直ちに対処する必要のない実行時エラーだが、通常はログに記録して監視すべき
     *
     * @param string $msg ログメッセージ
     */
    public static function errorLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->error($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:warning
     * エラーではない例外的な出来事
     *
     * @param string $msg ログメッセージ
     */
    public static function warningLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->warning($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:notice
     * 正常だが、重要な事象
     *
     * @param string $msg ログメッセージ
     */
    public static function noticeLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->notice($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:info
     * 興味深い事象
     *
     * @param string $msg ログメッセージ
     */
    public static function infoLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->info($msg, $context);
        }
    }

    /**
     * ログメッセージの出力
     *
     * ログレベル:debug
     * 詳細なデバッグ情報
     *
     * @param string $msg ログメッセージ
     */
    public static function debugLog(string $msg, array $context = [])
    {
        if (Config::IS_LOGFILE) {
            self::getInstance()->debug($msg, $context);
        }
    }
}

/**
 * ファイルサイズ問題
 * 古いログ削除問題
 */
