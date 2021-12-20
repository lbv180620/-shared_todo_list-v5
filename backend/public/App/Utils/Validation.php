<?php

declare(strict_types=1);

namespace App\Utils;

use App\Config\Config;
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
	public static function validateFormRequesut($post)
	{

		$result = [];

		// エラーメッセージ
		$err = [];

		// バリデーション
		if (!empty($post)) {

			if (isset($post['user_name'])) {

				// user_nameのバリデーション
				if (!filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
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
				if (!filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
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
				if (!filter_input(INPUT_POST, 'family_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
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
				if (!filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
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
				if (!filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
					$err['password'] = Config::MSG_PASSWORD_ERROR;
					Logger::errorLog(Config::MSG_PASSWORD_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
				}

				// 正規表現
				/**
				 *"/\A[a-z\d]{8,100}+\z/i"
				 *英小文字数字で8文字以上255文字以下の範囲で1回続く(大文字小文字は区別しない)パスワード
				 */
				if (!empty($password) && !preg_match("/\A[a-z\d]{8,255}+\z/i", $password)) {
					$err['password'] = Config::MSG_PASSWORD_REGEX_ERROR;
					Logger::errorLog(Config::MSG_PASSWORD_REGEX_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
				}
			}

			if (isset($post['password_confirm'])) {

				// password_confirmのバリデーション
				if (!filter_input(INPUT_POST, 'password_confirm', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
					$err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_ERROR;
					Logger::errorLog(Config::MSG_PASSWORD_CONFIRM_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
				}
				if (!empty($password) && !empty($password_confirm) && $password !== $password_confirm) {
					$err['password_confirm'] = Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR;
					Logger::errorLog(Config::MSG_PASSWORD_CONFIRM_MISMATCH_ERROR, ['file' => __FILE__, 'line' => __LINE__]);
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
}
