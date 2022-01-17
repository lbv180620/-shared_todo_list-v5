<?php

declare(strict_types=1);

namespace App\Config;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Monolog\Logger;

class Config
{
    /** DB関連 */

    /** @var array ドライバーオプション */
    // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
    const DRIVER_OPTS = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ];

    /** メッセージ関連 */
    const MSG_EXCEPTION_ERROR = "申し訳ございません。エラーが発生しました。";
    const MSG_PDOEXCEPTION_ERROR = "データベース接続に失敗しました。";

    // パスワードの正規表現
    const DEFAULT_PASSWORD_REGEXP = "/\A[a-z\d]{8,255}+\z/i";
    const JS_DEFAULT_PASSWORD_REGEXP = "^([a-z0-9]{8,255})$";
    const JS_DEFAULT_PASSWORD_REGEXFLG = "i";

    // register login
    const MSG_POST_SENDING_FAILURE_ERROR = "送信に失敗しました。";

    const MSG_USER_NAME_ERROR = "ユーザー名を記入してください。";
    const MSG_EMAIL_ERROR = "メールアドレスを記入してください。";
    const MSG_FAMILY_NAME_ERROR = "お名前(姓)を記入してください。";
    const MSG_FIRST_NAME_ERROR = "お名前(名)を記入してください。";
    const MSG_PASSWORD_ERROR = "パスワードを記入してください。";
    const MSG_PASSWORD_CONFIRM_ERROR = "確認用パスワードを記入してください。";

    const MSG_USER_NAME_STRLEN_ERROR = "ユーザー名は50文字以下にしてください。";
    const MSG_EMAIL_INCORRECT_ERROR = "メールアドレスの形式が正しくありません。";
    const MSG_EMAIL_STRLEN_ERROR = "メールアドレスは255文字以下にしてください。";
    const MSG_FAMILY_NAME_STRLEN_ERROR = "お名前(姓)は50文字以下にして下さい。";
    const MSG_FIRST_NAME_STRLEN_ERROR = "お名前(名)は50文字以下にして下さい。";
    const MSG_PASSWORD_REGEX_ERROR = "パスワードは英数字8文字以上255文字以下にして記入してください。";
    const MSG_PASSWORD_CONFIRM_MISMATCH_ERROR = "確認用パスワードと異なっています。";

    const MSG_USER_DUPLICATE = "既に同じメールアドレスが登録されています。";

    const MSG_NEW_REGISTRATION_SUCCESSFUL = "新規登録しました。ログインしてください。";

    const MSG_FAILURE_TO_LOGIN = "ログインに失敗しました。";
    const MSG_LOGIN_SUCCESSFUL = "ログインに成功しました。";

    const MSG_LOGOUT_FAILURE = 'ログアウトに失敗しました。';

    // entry edit
    const MSG_ITEM_NAME_ERROR = "項目名を入力してください。";
    const MSG_ITEM_NAME_STRLEN_ERROR = "項目名は100文字以下にしてください。";
    const MSG_STAFF_ID_ERROR = "担当者を選択してください。";
    const MSG_NOT_EXISTS_STAFF_ID_ERROR = "指定の担当者は存在しません。";
    const MSG_CONTENT_ERROR = "作業内容を入力してください。";
    const MSG_EXPIRATION_DATE_ERROR = "期限日を選択してください。";
    const MSG_INVAID_DATE_ERROR = "期限日の日付が正しくありません。";
    const MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR = "完了のチェックボックスの値が正しくありません。";
    // entry
    const MSG_TASK_REGISTRATION_SUCCESSFUL = "作業登録しました。";
    const MSG_TASK_REGISTRATION_FAILURE = "作業登録に失敗しました。";
    // edit
    const MSG_TASK_UPDATE_SUCCESSFUL = "作業を更新しました。";
    const MSG_TASK_UPDATE_FAILURE = "作業を更新できませんでした。";
    // delete
    const MSG_TASK_DELETE_SUCCESSFUL = "作業を削除しました。";
    const MSG_TASK_DELETE_FAILURE = "作業を削除できませんでした。";
    // complete
    const MSG_TASK_COMPLETE_SUCCESSFUL = "作業を完了しました。";
    const MSG_TASK_COMPLETE_FAILURE = "作業を完了できませんでした。";
    // cancel
    const MSG_USER_DELETE_SUCCESSFUL = "アカウントを削除しました。";
    const MSG_USER_DELETE_FAILURE = "アカウント削除に失敗しました。";

    /** JSバリデーション */
    const JS_SIGNUP_FORM_VALIDATION_ERROR_MSG_LIST =
    [
        // user_name
        'MSG_USER_NAME_ERROR' => self::MSG_USER_NAME_ERROR,
        'MSG_USER_NAME_STRLEN_ERROR' => self::MSG_USER_NAME_STRLEN_ERROR,
        // email
        'MSG_EMAIL_ERROR' => self::MSG_EMAIL_ERROR,
        'MSG_EMAIL_STRLEN_ERROR' => self::MSG_EMAIL_STRLEN_ERROR,
        'MSG_EMAIL_INCORRECT_ERROR' => self::MSG_EMAIL_INCORRECT_ERROR,
        // family_name
        'MSG_FAMILY_NAME_ERROR' => self::MSG_FAMILY_NAME_ERROR,
        'MSG_FAMILY_NAME_STRLEN_ERROR' => self::MSG_FAMILY_NAME_STRLEN_ERROR,
        // first_name
        'MSG_FIRST_NAME_ERROR' => self::MSG_FIRST_NAME_ERROR,
        'MSG_FIRST_NAME_STRLEN_ERROR' => self::MSG_FIRST_NAME_STRLEN_ERROR,
        // password
        'JS_DEFAULT_PASSWORD_REGEXP' => self::JS_DEFAULT_PASSWORD_REGEXP,
        'JS_DEFAULT_PASSWORD_REGEXFLG' => self::JS_DEFAULT_PASSWORD_REGEXFLG,
        'MSG_PASSWORD_ERROR' => self::MSG_PASSWORD_ERROR,
        'MSG_PASSWORD_REGEX_ERROR' => self::MSG_PASSWORD_REGEX_ERROR,
        // password_confirm
        'MSG_PASSWORD_CONFIRM_ERROR' => self::MSG_PASSWORD_CONFIRM_ERROR,
        'MSG_PASSWORD_CONFIRM_MISMATCH_ERROR' => self::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR,
    ];

    const JS_LOGIN_FORM_VALIDATION_ERROR_MSG_LIST =
    [
        // email
        'MSG_EMAIL_ERROR' => self::MSG_EMAIL_ERROR,
        'MSG_EMAIL_STRLEN_ERROR' => self::MSG_EMAIL_STRLEN_ERROR,
        'MSG_EMAIL_INCORRECT_ERROR' => self::MSG_EMAIL_INCORRECT_ERROR,
        // password
        'JS_DEFAULT_PASSWORD_REGEXP' => self::JS_DEFAULT_PASSWORD_REGEXP,
        'JS_DEFAULT_PASSWORD_REGEXFLG' => self::JS_DEFAULT_PASSWORD_REGEXFLG,
        'MSG_PASSWORD_ERROR' => self::MSG_PASSWORD_ERROR,
        'MSG_PASSWORD_REGEX_ERROR' => self::MSG_PASSWORD_REGEX_ERROR
    ];

    const JS_TODO_FORM_VALIDATION_ERROR_MSG_LIST =
    [
        // item_name
        'MSG_ITEM_NAME_ERROR' => self::MSG_ITEM_NAME_ERROR,
        'MSG_ITEM_NAME_STRLEN_ERROR' => self::MSG_ITEM_NAME_STRLEN_ERROR,
        // staff_id
        'MSG_STAFF_ID_ERROR' => self::MSG_STAFF_ID_ERROR,
        // content
        'MSG_CONTENT_ERROR' => self::MSG_CONTENT_ERROR,
        // expiration_date
        'MSG_EXPIRATION_DATE_ERROR' => self::MSG_EXPIRATION_DATE_ERROR,
        // 'MSG_INVAID_DATE_ERROR' => self::MSG_INVAID_DATE_ERROR,
        // 'MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR' => Config::MSG_INVAID_FINISHED_CHECKBOX_VALUE_ERROR
    ];

    /** ワンタイムトークン */
    /** @var int openssl_random_pseudo_bytes()で使用する文字列の長さ */
    const RAMDOM_PSEUDO_STRING_LENGTH = 32;

    /** @var string ワンタイムトークンが一致しないとき */
    const MSG_INVALID_PROCESS = '不正な処理が行われました。';

    /** ロギング設定 */
    const IS_LOGFILE = true;
    const DEFAULT_CHANNEL_NAME = 'local';
    const DEFAULT_LOG_LEVEL = Logger::WARNING;
    const DEFAULT_LOG_DIRNAME = __DIR__ . '/logs.d/';
    const DEFAULT_LOG_FILENAME = 'message.log';
    const DEFAULT_LOG_FILEPATH = self::DEFAULT_LOG_DIRNAME . self::DEFAULT_LOG_FILENAME;
    // const SIMPLE_FORMAT = '[%datetime%] %level_name%: %message%' . PHP_EOL;
    const SIMPLE_FORMAT = null;
    const SIMPLE_DATE_FORMAT = "Y年n月d日 H:i:s";

    /** 遅延ログアウト */
    // ログアウトの遅延時間
    const DEFAULT_DELAY_TIME = 10;
    // ログアウトの遅延処理
    const LOGOUT_SCRIPT = '<script type="text/javascript" async>
        const deferredLogout = () => {
            location.href = "../login/logout.php";
        }

        setTimeout(deferredLogout, ' . self::DEFAULT_DELAY_TIME * 1000 . ');
    </script>';

    /** アカウントロック */
    const MSG_ACOUNT_LOCKED_ERROR = "アカウントロックされてます。";
    const MSG_MAKE_ACOUNT_LOCKED = "アカウントがロックされました。解除したい場合は運営者に連絡してください。";
    const MSG_DELETED_USER_ERROR = "存在しないユーザです。";
    // アカウントロックの閾値
    const ACCOUNT_ROCK_THRESHOLD = 5;

    /** トランザクション */
    const MSG_TRANSACTION_INACTIVE_ERROR = "トランザクションがアクティブではありません";
}
