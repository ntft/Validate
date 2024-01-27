<?php
/**
 * ルール：時刻クラス
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
class RuleTime extends RuleBase
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

		// 第3引数に指定が無い場合
		if (is_array($this->infos) && count($this->infos) === 0 || $this->infos === '') {
			throw new Exception('Rule time : 第3引数に時刻の区切り文字を指定してください。');
		}

		// 区切り文字
		if (array_key_exists('delimiter', $this->infos)) {
			$delimiter = $this->infos['delimiter'];
		} else {
			$delimiter = NULL;
		}
		// 秒フラグ
		if (array_key_exists('second', $this->infos)) {
			$secFlg = $this->infos['second'];
		} else {
			$secFlg = TRUE;
		}
		// 分割個数
		if ($secFlg) {
			$divCnt = 3;
		} else {
			$divCnt = 2;
		}

		// 区切り文字を調べる
		if (strpos($value, $delimiter) === FALSE) {
			return sprintf($this->inies['not_delimiter'], $delimiter);
		}

		// 区切り文字で分割
		$hours = explode($delimiter, $value);
		$cnt = count($hours);
		// 時分(秒)のどれか1つでも足りない場合
		if ($cnt !== $divCnt) {
			return $this->inies['not_time'];
		}

		// 数値化
		foreach ($hours as $key => $hour) {
			$hours[$key] = (int)$hour;
		}

		// 時分：時刻として不正な場合
		if (($hours[0] < 0 || 23 < $hours[0]) ||
			($hours[1] < 0 || 59 < $hours[1])) {
			return $this->inies['not_time'];
		}

		// 秒：秒フラグが立っていた場合に検査する
		if ($divCnt == 3 && ($hours[2] < 0 || 59 < $hours[2])) {
			return $this->inies['not_time'];
		}

		return TRUE;
	}
}
?>