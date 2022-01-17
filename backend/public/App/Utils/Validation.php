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
     * 新規登録フォームからのリクエストを検証してエラーメッセージとリクエスト情報の入った連想配列を返す
     *
     * @param array $post
     * @return array $result 連想配列で、エラーメッセージ($result['err'])と記入情報($result['fill'])を返す
     */
    public static function validateSignUpFormRequest(array $post): array
    {

        $result = $err = [];

        if (!empty($post)) {

            // user_nameのバリデーション
            $result = self::validateUserName();
            if (!empty($result['err'])) $err['user_name'] = $result['err']['user_name'];
            if (!empty($result['post'])) $post['user_name'] = $result['post']['user_name'];

            // emailのバリデーション
            $result = self::validateEmail();
            if (!empty($result['err'])) $err['email'] = $result['err']['email'];
            if (!empty($result['post'])) $post['email'] = $result['post']['email'];

            // family_nameのバリデーション
            $result = self::validateFamilyName();
            if (!empty($result['err'])) $err['family_name'] = $result['err']['family_name'];
            if (!empty($result['post'])) $post['family_name'] = $result['post']['family_name'];

            // first_nameのバリデーション
            $result = self::validateFirstName();
            if (!empty($result['err'])) $err['first_name'] = $result['err']['first_name'];
            if (!empty($result['post'])) $post['first_name'] = $result['post']['first_name'];

            // passwordのバリデーション
            $result = self::validatePassword();
            if (!empty($result['err'])) $err['password'] = $result['err']['password'];

            // password_confirmのバリデーション
            $result = self::validatePasswordConfirm($post['password']);
            if (!empty($result['err'])) $err['password_confirm'] = $result['err']['password_confirm'];
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
     * ログインフォームからのリクエストを検証してエラーメッセージとリクエスト情報の入った連想配列を返す
     *
     * @param array $post
     * @return array $result 連想配列で、エラーメッセージ($result['err'])と記入情報($result['fill'])を返す
     */
    public static function validateLoginFormRequest(array $post): array
    {
        $result = $err = [];

        if (!empty($post)) {

            // emailのバリデーション
            $result = self::validateEmail();
            if (!empty($result['err'])) $err['email'] = $result['err']['email'];
            if (!empty($result['post'])) $post['email'] = $result['post']['email'];

            // passwordのバリデーション
            $result = self::validatePassword();
            if (!empty($result['err'])) $err['password'] = $result['err']['password'];
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
     * 作業登録フォームからのリクエストを検証してエラーメッセージとリクエスト情報の入った連想配列を返す
     *
     * @param array $post
     * @return array $result 連想配列で、エラーメッセージ($result['err'])と記入情報($result['fill'])を返す
     */
    public static function validateTodoFormRequest(array $post): array
    {
        $result = $err = [];

        if (!empty($post)) {

            // item_nameのバリデーション
            $result = self::validateItemName();
            if (!empty($result['err'])) $err['item_name'] = $result['err']['item_name'];
            if (!empty($result['post'])) $post['item_name'] = $result['post']['item_name'];

            // staff_idのバリデーション
            $result = self::validateStaffId($post);
            if (!empty($result['err'])) $err['staff_id'] = $result['err']['staff_id'];

            // contentのバリデーション
            $result = self::validateContent();
            if (!empty($result['err'])) $err['content'] = $result['err']['content'];
            if (!empty($result['post'])) $post['content'] = $result['post']['content'];

            // expiration_dateのバリデーション
            $result = self::validateExpirationDate($post);
            if (!empty($result['err'])) $err['expiration_date'] = $result['err']['expiration_date'];

            // finishedのバリデーション
            $result = self::validateFinished($post);
            if (!empty($result['err'])) $err['finished'] = $result['err']['finished'];
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
     * user_nameのバリデーション
     *
     * @return array
     */
    private static function validateUserName(): array
    {
        $result = $err = $post = [];

        if (!$user_name = self::filterInput('user_name')) {
            $err['user_name'] = Config::MSG_USER_NAME_ERROR;
            $post['user_name'] = "";
            Logger::errorLog(Config::MSG_USER_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        /**
         * 文字数制限
         * varchar(50)
         */
        if (!self::isValidStringLength($user_name, 50)) {
            $err['user_name'] = Config::MSG_USER_NAME_STRLEN_ERROR;
            $post['user_name'] = "";
            Logger::errorLog(Config::MSG_USER_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        $result['post'] = $post;
        return $result;
    }

    /**
     * emailのバリデーション
     *
     * @return array
     */
    private static function validateEmail(): array
    {
        $result = $err = $post = [];

        if (!$email = self::filterInput('email')) {
            $err['email'] = Config::MSG_EMAIL_ERROR;
            $post['email'] = "";
            Logger::errorLog(Config::MSG_EMAIL_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        // メールアドレスの形式チェック
        if (!self::isValidEmail($email)) {
            $err['email'] = Config::MSG_EMAIL_INCORRECT_ERROR;
            $post['email'] = "";
            Logger::errorLog(Config::MSG_EMAIL_INCORRECT_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        /**
         * 文字数制限
         * varchar(255)
         */
        if (!self::isValidStringLength($email, 255)) {
            $err['email'] = Config::MSG_EMAIL_STRLEN_ERROR;
            $post['email'] = "";
            Logger::errorLog(Config::MSG_EMAIL_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        $result['post'] = $post;
        return $result;
    }

    /**
     * family_nameのバリデーション
     *
     * @return array
     */
    private static function validateFamilyName(): array
    {
        $result = $err = $post = [];

        if (!$family_name = self::filterInput('family_name')) {
            $err['family_name'] = Config::MSG_FAMILY_NAME_ERROR;
            $post['family_name'] = "";
            Logger::errorLog(Config::MSG_FAMILY_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        /**
         * 文字数制限
         * varchar(50)
         */
        if (!self::isValidStringLength($family_name, 50)) {
            $err['family_name'] = Config::MSG_FAMILY_NAME_STRLEN_ERROR;
            $post['family_name'] = "";
            Logger::errorLog(Config::MSG_FAMILY_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        $result['post'] = $post;
        return $result;
    }

    /**
     * first_nameのバリデーション
     *
     * @return array
     */
    private static function validateFirstName(): array
    {
        $result = $err = $post = [];

        if (!$first_name = self::filterInput('first_name')) {
            $err['first_name'] = Config::MSG_FIRST_NAME_ERROR;
            $post['first_name'] = "";
            Logger::errorLog(Config::MSG_FIRST_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        /**
         * 文字数制限
         * varchar(50)
         */
        if (!self::isValidStringLength($first_name, 50)) {
            $err['first_name'] = Config::MSG_FIRST_NAME_STRLEN_ERROR;
            $post['first_name'] = "";
            Logger::errorLog(Config::MSG_FIRST_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        $result['post'] = $post;
        return $result;
    }

    /**
     * passwordのバリデーション
     *
     * @return array
     */
    private static function validatePassword(): array
    {
        $err = [];

        if (!$password = self::filterInput('password')) {
            $err['password'] = Config::MSG_PASSWORD_ERROR;
            Logger::errorLog(Config::MSG_PASSWORD_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        // 正規表現
        /**
         *"/\A[a-z\d]{8,100}+\z/i"
         *英小文字数字で8文字以上255文字以下の範囲で1回続く(大文字小文字は区別しない)パスワード
         */
        if (!self::isValidPassword($password)) {
            $err['password'] = Config::MSG_PASSWORD_REGEX_ERROR;
            Logger::errorLog(Config::MSG_PASSWORD_REGEX_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        return $result;
    }

    /**
     * password_confirmのバリデーション
     *
     * @param string $password
     * @return array
     */
    private static function validatePasswordConfirm(string $password): array
    {
        $result = $err = [];

        if (!$password_confirm = self::filterInput('password_confirm')) {
            $err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_ERROR;
            Logger::errorLog(Config::MSG_PASSWORD_CONFIRM_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        if (!self::isValidConfirmedPassword($password, $password_confirm)) {
            $err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR;
            Logger::errorLog(Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        return $result;
    }

    /**
     * item_nameのバリデーション
     *
     * @return array
     */
    private static function validateItemName(): array
    {
        $result = $err = $post = [];

        if (!$item_name = self::filterInput('item_name')) {
            $err['item_name'] = Config::MSG_ITEM_NAME_ERROR;
            $post['item_name'] = "";
            Logger::errorLog(Config::MSG_ITEM_NAME_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }
        /**
         * 文字数制限
         * varchar(100)
         */
        if (!self::isValidStringLength($item_name, 100)) {
            $err['item_name'] = Config::MSG_ITEM_NAME_STRLEN_ERROR;
            $post['item_name'] = "";
            Logger::errorLog(Config::MSG_ITEM_NAME_STRLEN_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        $result['post'] = $post;
        return $result;
    }

    /**
     * staff_idのバリデーション
     *
     * @param array $post
     * @return array
     */
    private static function validateStaffId(array $post): array
    {
        $result = $err = [];

        // 担当者がいるかどうか
        if (!self::isValidUserId($post['staff_id'])) {
            $err['staff_id'] = Config::MSG_NOT_EXISTS_STAFF_ID_ERROR;
            Logger::errorLog(Config::MSG_NOT_EXISTS_STAFF_ID_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        return $result;
    }

    /**
     * contentのバリデーション
     *
     * @return array
     */
    private static function validateContent(): array
    {
        $result = $err = $post = [];

        if (!self::filterInput('content', false)) {
            $err['content'] = Config::MSG_CONTENT_ERROR;
            $post['content'] = "";
            Logger::errorLog(Config::MSG_CONTENT_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        $result['post'] = $post;
        return $result;
    }

    /**
     * expiration_dateのバリデーション
     *
     * @param array $post
     * @return array
     */
    private static function validateExpirationDate(array $post): array
    {
        $result = $err = [];

        // 正しい日付かどうか判定
        if (!self::isValidDate($post['expiration_date'])) {
            $err['expiration_date'] = Config::MSG_INVAID_DATE_ERROR;
            Logger::errorLog(Config::MSG_INVAID_DATE_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
        return $result;
    }

    /**
     * finishedのバリデーション
     *
     * @param array $post
     * @return array
     */
    private static function validateFinished(array $post): array
    {
        $result = $err = [];

        // 0 null "" [] じゃない
        if (!empty($post['finished']) && $post['finished'] !== 1) {
            $err['finished'] = Config::MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR;
            Logger::errorLog(Config::MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
        }

        $result['err'] = $err;
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

    /**
     * input_filter
     *
     * @param string $str
     * @param bool $trim_flg
     */
    private static function filterInput(string $str, $trim_flg = true): string
    {
        $result = filter_input(INPUT_POST, $str, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $trim_flg ? trim($result) : $result;
    }

    /**
     * 文字数制限
     *
     * @param string $var_name
     * @param int $str_length
     * @return bool
     */
    private static function isValidStringLength(string $var_name, int $str_length): bool
    {
        return empty($var_name) || mb_strlen($var_name) <= $str_length;
    }

    /**
     * 正しいメールアドレスの形式かどうか
     *
     * @param string $email
     * @return bool
     */
    private static function isValidEmail(string $email): bool
    {
        return empty($email) || $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 正しいパスワードかどうか
     *
     * @param string $password
     * @return bool
     */
    private static function isValidPassword(string $password): bool
    {
        return empty($password) || preg_match(Config::DEFAULT_PASSWORD_REGEXP, $password);
    }

    /**
     * パスワードと確認用パスードが一致するかどうか
     *
     * @param string $password
     * @param string $password_confirm
     */
    private static function isValidConfirmedPassword($password, $password_confirm): bool
    {
        return empty($password_confirm) || $password === $password_confirm;
    }
}
