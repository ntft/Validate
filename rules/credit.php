<?php
/**
 * ルール：クレジットカード番号クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/10
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleCredit extends RuleBase
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

		// 半角数字以外を除去
		$value = preg_replace('/[^\d]/', '', $value);

		$len = strlen($value);
		// 文字列長が13～16桁の範囲外
		if ($len < 13 || 16 < $len) {
			return $this->inies['not_credit_length'];
		}

		// チェックデジット
		if ($this->_checkDigit($value)) {
			return TRUE;
		}
		else {
			return $this->inies['not_credit'];
		}
	}

	/**
	 * クレジットカード番号を検査する
	 *
	 * @param string $credit クレジットカード番号
	 * @return boolean TRUE(OK) / FALSE(NG)
	 * @reference
	 * 	 Anatomy of Credit Card Numbers
	 * 	 http://www.merriampark.com/anatomycc.htm
	 * @memo
	 *	末尾から数えて偶数桁を2倍、その値が10を超える場合は9引く。
	 *	全ての値を合計し、10の倍数の場合、正しいクレジットカード番号。
	 */
	private function _checkDigit($credit)
	{
		$timesTwo = FALSE;
		$sum = 0;
		// 末尾から処理する
		for($ii = strlen($credit) - 1; $ii >= 0; $ii--) {
			$digit = (int)substr($credit, $ii, 1);

			// 偶数桁の場合
			if ($timesTwo) {
				$addend = $digit * 2;
				// 10以上の場合
				if ($addend >= 10) {
					// 9引く
					$addend -= 9;
				}
			}
			else {
				$addend = $digit;
			}
			$sum += $addend;
			$timesTwo = !$timesTwo;
		}
		return ($sum % 10 == 0);
	}
}
?>