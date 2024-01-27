<?php
/**
 * ルール：日付クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/11
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleDate extends RuleBase
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

		// 第3引数が配列、かつキー「delimiter」が存在する
		if (is_array($this->infos) && array_key_exists('delimiter', $this->infos)) {
			$delimiter = $this->infos['delimiter'];
		} else {
			throw new Exception('Rule date : 第3引数にキー"delimiter"を持つ配列を指定してください。');
		}

		// 日フラグ
		if (array_key_exists('day', $this->infos)) {
			$dayFlg = $this->infos['day'];
		} else {
			$dayFlg = TRUE;
		}

		// 分割個数
		if ($dayFlg) {
			$divCnt = 3;
		} else {
			$divCnt = 2;
		}

		// 区切り文字を調べる
		if (strpos($value, $delimiter) === FALSE) {
			return sprintf($this->inies['not_delimiter'], $delimiter);
		}

		// 区切り文字で分割
		$ymds = explode($delimiter, $value);
		$cnt = count($ymds);
		// 年月日のどれか1つでも足りない場合
		if ($cnt !== $divCnt) {
			return $this->inies['not_date'];
		}

		// 日フラグ
		if ($dayFlg) {
			$day = $ymds[2];
		} else {
			$day = 1;
		}

		// 日付として正しくない場合
		if (! checkdate($ymds[1], $day, $ymds[0])) {
			return $this->inies['not_date'];
		}

		return TRUE;
	}
}
?>