<?php
/**
 * ルール：メールアドレスクラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/09
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleMail extends RuleBase
{
	// メールアドレス形式(フレームワークEthnaから拝借)
	const MAIL_PATTERN = '/^([a-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// 親クラスのコンストラクタをコール
		parent::__construct(__FILE__);
	}

	/**
	 * バリデートする
	 *
	 * @access public
	 * @return boolean TRUE(OK) / string エラーメッセージ(NG)
	 * @cation 未入力('')の場合はTRUEを返す
	 * @refarence
	 *	re: PHPでメールアドレスかどうか調べる方法 (ハズレ日記)
	 *	http://catbot.net/blog/2007/06/re_php.html
	 */
	public function run()
	{
		// 未入力の場合
		if ($this->value === '') {
			return TRUE;
		}

		// 値
		$value = trim($this->value);

		// メールアドレスチェック
		if (preg_match(self::MAIL_PATTERN, $value)) {
			return TRUE;
		}
		else {
			return $this->inies['not_mail'];
		}
	}
}
?>