<?php

declare(strict_types=1);

namespace App\Utils;

use App\Config\Config;

/**
 * 共通関数クラスです。
 * セキュリティ対策用の関数など定義。
 */
class Common
{
    /**
     * XSS対策：エスケープ処理
     * @static@method h
     * @param string $str 対象の文字列
     * @return string 処理された文字列
     */
    public static function h(?string $str): string
    {
        if (is_null($str)) {
            exit;
        }
        return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
    }

    /**
     * サニタイズ：POSTまたはGETで送信されて来た連想配列の要素の値をサニタイズする(1次配列のみ)
     * @static@method sanitize
     * @param array $post POSTまたはGETで送信されて来た連想配列(1次配列)
     * @return array $post エスケープ処理が完了した連想配列
     */
    public static function sanitize(array $post): array
    {
        foreach ($post as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            $post[$k] = htmlspecialchars($v, ENT_QUOTES, "UTF-8");
        }
        return $post;
    }

    /**
     * 指定の長さのランダムな文字列を作成
     *
     * @param int $length 作成する文字列の長さ(初期値：32)
     * @return string
     */
    public static function makeRandomString(int $length = 32): string
    {
        // openssl_random_pseudo_bytesでも可
        return bin2hex(random_bytes($length));
    }

    /**
     * ワンタイムトークンを発生させる
     *
     * @param string $tokenName セッションに保存するトークンのキーの名前(初期値：token)
     * @return string
     */
    public static function generateToken(string $tokenName = 'token'): string
    {
        // ワンタイムトークンを生成してセッションに保存する
        $token = self::makeRandomString(Config::RAMDOM_PSEUDO_STRING_LENGTH);
        $_SESSION[$tokenName] = $token;
        return $token;
    }

    /**
     * 送信されてきたトークンが正しいかどうか調べる
     *
     * @param string $token 送信されてきたトークン(nullable対応)
     * @param string $tokenName セッションに保存されているトークンキー名
     * @return bool
     */
    public static function isValidToken(?string $token, string $tokenName = 'token'): bool
    {
        // セッションにトークンが登録されていない | セッションに登録されているトークンと送信されてきたトークンが一致しない
        if (!isset($_SESSION[$tokenName]) || $_SESSION[$tokenName] !== $token) {
            return false;
        }
        return true;
    }

    /**
     * Guestユーザかどうか
     *
     * @return bool
     */
    public static function isGuestUser(): bool
    {
        if (!isset($_SESSION['login'])) return true;

        return false;
    }

    /**
     * Authユーザかどうか
     *
     * @return bool
     */
    public static function isAuthUser(): bool
    {
        if (isset($_SESSION['login'])) return true;

        return false;
    }

    /**
     * 文字列の比較処理(Config::MSG_USER_DELETE_SUCCESSFULと比較)
     *
     * @param string $str1 サクセスメッセージ
     * @param string $str2 初期値：Config::MSG_USER_DELETE_SUCCESSFUL
     * @return bool
     */
    public static function checkStringForLogout(string $str1, string $str2 = Config::MSG_USER_DELETE_SUCCESSFUL): bool
    {
        if ($str1 === Config::MSG_USER_DELETE_SUCCESSFUL && strcmp($str1, $str2) === 0) {
            return true;
        }

        return false;
    }

    /**
     * トランザクションの開始処理
     *
     * @param \PDO $dbh
     */
    public static function beginTransaction(\PDO $dbh)
    {
        $dbh->beginTransaction();

        if (!$dbh->inTransaction()) {
            throw new \Exception(Config::MSG_TRANSACTION_INACTIVE_ERROR);
        }
    }

    /**
     * トランザクションのコミット処理
     *
     * @param \PDO $dbh
     * @return bool
     */
    public static function commit(\PDO $dbh): bool
    {
        if (!$dbh->inTransaction()) {
            throw new \Exception(Config::MSG_TRANSACTION_INACTIVE_ERROR);
        }

        return $dbh->commit();
    }

    /**
     * トランザクションのロールバック処理
     *
     * @param \PDO $dbh
     */
    public static function rollBack(\PDO $dbh)
    {
        if (!$dbh->inTransaction()) {
            throw new \Exception(Config::MSG_TRANSACTION_INACTIVE_ERROR);
        }

        $dbh->rollBack();
    }

    /**
     * 正しいリクエストかチェック
     *
     * @param string $method
     * @return bool
     */
    public static function isValidRequest(string $method): bool
    {
        return $_SERVER['REQUEST_METHOD'] === $method;
    }

    /**
     * URLの取得
     *
     * @param string $path
     * @return string
     */
    
    public static function getUrl($path)
    {
        return (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . '/' . $path;
    }
}
