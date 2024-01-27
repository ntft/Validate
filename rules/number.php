<?php
/**
 * ルール：数値クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/06
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleNumber extends RuleBase
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

		// 数値形式に不一致
		if (! preg_match('/^[+-]?\d+(\.\d+)?$/', (string)$value)) {
			return $this->inies['not_num'];
		}

		// PHPの実数値として扱えるか調べる
		if ($this->infos === TRUE) {
			// 範囲外の場合
			if (is_infinite($value)) {
				return $this->inies['num_range'];
			}
		}

		return TRUE;
	}
}
?>