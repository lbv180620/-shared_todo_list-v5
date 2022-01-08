<?php

declare(strict_types=1);

namespace App\Utils;

use App\Config\Config;
use App\Models\Base;
use App\Models\Users;
use App\Utils\Logger;

/**
 * 検証クラス
 * フォームリクエストのバリデーションに関するメソッドを定義
 */
class Validation
{


    /**
     * フォームリクエストを検証してエラーメッセージとリクエスト情報の入った連想配列を返す
     *
     * @param array $post
     * @return array $result 連想配列で、エラーメッセージ($result['err'])と記入情報($result['fill'])を返す
     */
    public static function validateFormRequesut(array $post): array
    {

        $result = [];

        // エラーメッセージ
        $err = [];

        // バリデーション
        if (!empty($post)) {

            if (isset($post['user_name'])) {

                // user_nameのバリデーション
                if (!$user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['user_name'] = Config::MSG_USER_NAME_ERROR;
                    $post['user_name'] = "";
                    Logger::errorLog(Config::MSG_USER_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                /**
                 * 文字数制限
                 * varchar(50)
                 */
                if (!empty($user_name) && mb_strlen($user_name) > 50) {
                    $err['user_name'] = Config::MSG_USER_NAME_STRLEN_ERROR;
                    $post['user_name'] = "";
                    Logger::errorLog(Config::MSG_USER_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['email'])) {

                // emailのバリデーション
                if (!$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['email'] = Config::MSG_EMAIL_ERROR;
                    $post['email'] = "";
                    Logger::errorLog(Config::MSG_EMAIL_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                // メールアドレスの形式チェック
                if (!empty($email) && !$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $err['email'] = Config::MSG_EMAIL_INCORRECT_ERROR;
                    $post['email'] = "";
                    Logger::errorLog(Config::MSG_EMAIL_INCORRECT_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                /**
                 * 文字数制限
                 * varchar(255)
                 */
                if (!empty($email) && mb_strlen($email) > 255) {
                    $err['email'] = Config::MSG_EMAIL_STRLEN_ERROR;
                    $post['email'] = "";
                    Logger::errorLog(Config::MSG_EMAIL_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['family_name'])) {

                // family_nameのバリデーション
                if (!$family_name = filter_input(INPUT_POST, 'family_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['family_name'] = Config::MSG_FAMILY_NAME_ERROR;
                    $post['family_name'] = "";
                    Logger::errorLog(Config::MSG_FAMILY_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                /**
                 * 文字数制限
                 * varchar(50)
                 */
                if (!empty($family_name) && mb_strlen($family_name) > 50) {
                    $err['family_name'] = Config::MSG_FAMILY_NAME_STRLEN_ERROR;
                    $post['family_name'] = "";
                    Logger::errorLog(Config::MSG_FAMILY_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['first_name'])) {

                // first_nameのバリデーション
                if (!$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['first_name'] = Config::MSG_FIRST_NAME_ERROR;
                    $post['first_name'] = "";
                    Logger::errorLog(Config::MSG_FIRST_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                /**
                 * 文字数制限
                 * varchar(50)
                 */
                if (!empty($first_name) && mb_strlen($first_name) > 50) {
                    $err['first_name'] = Config::MSG_FIRST_NAME_STRLEN_ERROR;
                    $post['first_name'] = "";
                    Logger::errorLog(Config::MSG_FIRST_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['password'])) {

                // passwordのバリデーション
                if (!$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['password'] = Config::MSG_PASSWORD_ERROR;
                    Logger::errorLog(Config::MSG_PASSWORD_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }

                // 正規表現
                /**
                 *"/\A[a-z\d]{8,100}+\z/i"
                 *英小文字数字で8文字以上255文字以下の範囲で1回続く(大文字小文字は区別しない)パスワード
                 */
                if (!empty($password) && !preg_match(Config::DEFAULT_PASSWORD_REGEXP, $password)) {
                    $err['password'] = Config::MSG_PASSWORD_REGEX_ERROR;
                    Logger::errorLog(Config::MSG_PASSWORD_REGEX_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['password_confirm'])) {

                // password_confirmのバリデーション
                if (!$password_confirm = filter_input(INPUT_POST, 'password_confirm', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_ERROR;
                    Logger::errorLog(Config::MSG_PASSWORD_CONFIRM_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                if (!empty($password) && !empty($password_confirm) && $password !== $password_confirm) {
                    $err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR;
                    Logger::errorLog(Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['item_name'])) {
                // item_nameのバリデーション
                if (!$item_name = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['item_name'] = Config::MSG_ITEM_NAME_ERROR;
                    $post['item_name'] = "";
                    Logger::errorLog(Config::MSG_ITEM_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
                /**
                 * 文字数制限
                 * varchar(100)
                 */
                if (!empty($item_name) && mb_strlen($item_name) > 100) {
                    $err['item_name'] = Config::MSG_ITEM_NAME_STRLEN_ERROR;
                    $post['item_name'] = "";
                    Logger::errorLog(Config::MSG_ITEM_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['staff_id'])) {
                // 担当者がいるかどうか
                if (!self::isValidUserId($post['staff_id'])) {
                    $err['staff_id'] = Config::MSG_NOT_EXISTS_STAFF_ID_ERROR;
                    Logger::errorLog(Config::MSG_NOT_EXISTS_STAFF_ID_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['content'])) {
                // contentのバリデーション
                if (!$item_name = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $err['content'] = Config::MSG_CONTENT_ERROR;
                    $post['content'] = "";
                    Logger::errorLog(Config::MSG_CONTENT_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['expiration_date'])) {
                // 正しい日付かどうか判定
                if (!self::isValidDate($post['expiration_date'])) {
                    $err['expiration_date'] = Config::MSG_INVAID_DATE_ERROR;
                    Logger::errorLog(Config::MSG_INVAID_DATE_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }

            if (isset($post['finished'])) {
                // finishedのバリデーション
                // 0 null "" [] じゃない
                if (!empty($post['finished']) && $post['finished'] !== 1) {
                    $err['finished'] = Config::MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR;
                    Logger::errorLog(Config::MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
                }
            }
        } else {
            $err['msg'] = Config::MSG_POST_SENDING_FAILURE_ERROR;
            Logger::errorLog(Config::MSG_POST_SENDING_FAILURE_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        // エラーメッセージ情報
        $result['err'] = $err;
        // 記入情報
        $result['fill'] = $post;

        return $result;
    }

    /**
     * 指定IDのユーザが存在するかどうか判定
     *
     * @param string $staff_id ユーザID(文字列)
     * @return bool
     */
    private static function isValidUserId(string $staff_id): bool
    {
        $staff_id = (int)$staff_id;

        // $staff_idが数字でなかったら、falseを返却
        if (!is_numeric($staff_id)) {
            return false;
        }

        // $staff_idが0以下はありえないので、falseを返却
        if ($staff_id <= 0) {
            return false;
        }

        // UsersクラスのisExistsUser()メソッドを使って、該当のユーザを検索した結果を返却
        try {
            $base = Base::getPDOInstance();
            $dbh = new Users($base);
            return $dbh->isExistsUser($staff_id);
        } catch (\PDOException $e) {

            $_SESSION['err']['msg'] = Config::MSG_PDOEXCEPTION_ERROR;
            Logger::errorLog(Config::MSG_PDOEXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
            header('Location: ../../public/error/error.php', true, 301);
            exit;
        } catch (\Exception $e) {

            $_SESSION['err']['msg'] = Config::MSG_EXCEPTION_ERROR;
            Logger::errorLog(Config::MSG_EXCEPTION_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
            header('Location: ../../public/error/error.php', true, 301);
            exit;
        }
    }

    /**
     * 正しい日付形式の文字列かどうかを判定
     *
     * @param string $date 日付形式の文字列
     * @return bool 正しいとき：true、正しくないとき：false
     */
    private static function isValidDate(string $date): bool
    {
        // strtotime()関数を使って、タイムスタンプに変換できるかどうかで正しい日付かどうかを調べる
        return strtotime($date) === false ? false : true;
    }
}
