<?php
/**
 * ルール：必須入力クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/05
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleRequired extends RuleBase
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
	 */
	public function run()
	{
		// 値
		$value = trim($this->value);

		// 文字数が1以上の場合
		if(strlen($value) > 0) {
			return TRUE;
		} else {
			// 未入力時のエラー
			return $this->inies['not_input'];
		}
	}
}
?>