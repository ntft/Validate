<?php
/**
 * ルール：カタカナクラス
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
class RuleKatakana extends RuleBase
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
	 * @refarense
	 *	» [CodeIgniter] Formバリデーションの拡張クラス Web Sytem | AIDREAM
	 *	http://blog.aidream.jp/codeigniter/codeigniter-form-validation-extend-class-1351.html
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

		// UTF-8以外の場合
		if ($encoding != 'UTF-8') {
			// UTF-8に変換
			$value = mb_convert_encoding($value, 'UTF-8', $encoding);
		}

		// 正規表現パターン
		$ptnMulti	= '/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|ー)+$/';
		$ptnSingle	= '/^(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])+$/';

		// 全角・半角カタカナチェック
		if (is_array($this->infos) && count($this->infos) === 0) {
			if(preg_match($ptnMulti, $value) || preg_match($ptnSingle, $value)) {
				return TRUE;
			} else {
				return $this->inies['not_katakana'];
			}
		}
		// 全角カタカナチェック
		else if ($this->infos === TRUE) {
			if(preg_match($ptnMulti, $value)) {
				return TRUE;
			} else {
				return $this->inies['not_katakana_multi'];
			}
		}
		// 半角カタカナチェック
		else if ($this->infos === FALSE) {
			if(preg_match($ptnSingle, $value)) {
				return TRUE;
			} else {
				return $this->inies['not_katakana_single'];
			}
		}
		// 不正な指定
		else {
			throw new Exception('Rule katakana : "katakana"の第3引数には、TRUE or FALSE、または何も指定しないでください。');
		}
	}
}
?>