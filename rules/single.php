<?php
/**
 * ルール：半角文字クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/08
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleSingle extends RuleBase
{
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
	 * @exception Exception validate.php run()で捕捉する
	 * @cation 未入力('')の場合はTRUEを返す
	 */
	public function run()
	{
		// 未入力の場合
		if ($this->value === '') {
			return TRUE;
		}

		// 値
		$value = trim($this->value);
		// 文字コードの取得
		$encoding = mb_detect_encoding($value);
		if ($encoding === FALSE) {
			throw new Exception('mb_detect_encoding：文字コードの取得に失敗しました。');
		}

		// 文字数取得
		$len	= strlen($value);
		$mbLen	= mb_strlen($value, $encoding);
		// strlen()とmb_strlen()が一致(半角文字のみ)
		if ($len === $mbLen) {
			return TRUE;
		}
		else {
			return $this->inies['not_single'];
		}
	}
}
?>