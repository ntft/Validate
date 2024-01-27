<?php
/**
 * ルール：バイト長クラス
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
class RuleByte extends RuleBase
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
		// 文字数取得
		$len = strlen($value);

		$min = $max = NULL;

		// 最小値が存在する場合
		if (array_key_exists('min', $this->infos)) {
			$min = (int)$this->infos['min'];
			// 最小値未満の場合
			if ($len < $min) {
				return sprintf($this->inies['min'], $min);
			}
		}
		// 最大値が存在する場合
		if (array_key_exists('max', $this->infos)) {
			$max = (int)$this->infos['max'];
			// 最大値より大きい場合
			if ($len > $max) {
				return sprintf($this->inies['max'], $max);
			}
		}
		// minもmaxも存在しない場合
		if ($min === NULL && $max === NULL) {
			throw new Exception('Rule byte：少なくともmin, maxのどちらかが必要です。');
		}
		return TRUE;
	}
}
?>