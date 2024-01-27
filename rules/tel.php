<?php
/**
 * ルール：電話番号クラス
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
class RuleTel extends RuleBase
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

		// ハイフンを含まない場合
		if (mb_strpos($value, '-') === FALSE) {
			return $this->inies['not_hyphen'];
		}

		// 正規表現パターン
		$ptnFixed = '/^(0(?:[1-9]|[1-9]{2}\d{0,2}))-([2-9]\d{0,3})-(\d{4})$/';
		$ptnMobile = '/^(0[57-9]0)-(\d{4})-(\d{4})$/';

		// 固定・携帯電話番号チェック
		if (is_array($this->infos) && count($this->infos) === 0) {
			// 固定・携帯電話番号形式
			if (preg_match($ptnFixed, $value, $fixedMatches) || preg_match($ptnMobile, $value, $mobileMatches)) {
				// 固定：10桁
				if (count($fixedMatches) === 4 && strlen($fixedMatches[1] . $fixedMatches[2] . $fixedMatches[3]) === 10) {
					return TRUE;
				}
				// 固定：11桁
				elseif (count($mobileMatches) === 4 && strlen($mobileMatches[1] . $mobileMatches[2] . $mobileMatches[3]) === 11) {
					return TRUE;
				}
			}
			return $this->inies['not_tel'];
		}
		// 固定電話番号チェック
		else if ($this->infos === TRUE) {
			// 固定電話番号形式、かつ10桁
			if (preg_match($ptnFixed, $value, $matches) && count($matches) === 4 &&
				strlen($matches[1] . $matches[2] . $matches[3]) === 10) {
				return TRUE;
			}
			else {
				return $this->inies['not_fixed_tel'];
			}
		}
		// 携帯電話番号チェック
		else if ($this->infos === FALSE) {
			// 携帯電話番号形式、かつ11桁
			if (preg_match($ptnMobile, $value, $matches) && count($matches) === 4 &&
				strlen($matches[1] . $matches[2] . $matches[3]) === 11) {
				return TRUE;
			}
			else {
				return $this->inies['not_mobile'];
			}
		}
		else {
			throw new Exception('Rule tel : "tel"の第3引数には、TRUE or FALSE、または何も指定しないでください。');
		}
	}
}
?>