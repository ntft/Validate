<?php
/**
 * ルール：整数クラス
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
class RuleInteger extends RuleBase
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

		// INT型の最小値
		$phpIntMin = -PHP_INT_MAX - 1;

		// 値
		$value = trim($this->value);

		// 整数形式に不一致
		if (! preg_match('/^[+-]?\d+$/', (string)$value)) {
			return $this->inies['not_int'];
		}

		// PHPの最小値～最大値の範囲内か調べる
		if ($this->infos === TRUE) {
			// 範囲外の場合
			if ($value < $phpIntMin || PHP_INT_MAX < $value) {
				return sprintf($this->inies['int_range'], $phpIntMin, PHP_INT_MAX);
			}
		}

		return TRUE;
	}
}
?>