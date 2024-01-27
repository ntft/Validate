<?php
/**
 * ルール：比較クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/19
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @cation PHP 5.0 以上必須
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleComparison extends RuleBase
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
	 * @cation 全て未入力('')の場合はTRUEを返す
	 */
	public function run()
	{
		// 値
		$value = trim($this->value);
		// 演算子の種類
		$opeType = array('>', '>=', '==', '<=', '<');

		if (is_array($this->infos)) {
			// names
			if (array_key_exists('names', $this->infos)) {
				$names = $this->infos['names'];
			}
			// キー未指定
			if (! isset($names)) {
				throw new Exception('Rule comparison : 第3引数の配列にはキー"names"を指定してください。');
			}
			// 配列でない場合
			if (! is_array($names)) {
				throw new Exception('Rule comparison : 第3引数の配列にはキー"names"には配列を指定してください。');
			}
			// 要素数が1未満
			if (count($names) < 1) {
				throw new Exception('Rule comparison : 要素数1以上の配列を指定してください。');
			}

			// operator
			if (array_key_exists('operator', $this->infos)) {
				$operator = $this->infos['operator'];
			}
			else {
				throw new Exception('Rule comparison : キー"operator"を指定してください。');
			}
			// 想定外の演算子
			if (! in_array($operator, $opeType)) {
				throw new Exception('Rule comparison : "operator"には、演算子( >, >=, ==, <=, < )を指定してください。');
			}

			// message
			if (array_key_exists('message', $this->infos)) {
				$message = $this->infos['message'];
			}
			// キー未指定
			if (! isset($message)) {
				throw new Exception('Rule comparison : キー"message"を指定してください。');
			}
		}
		// 配列でない場合
		else {
			throw new Exception('Rule comparison : 第3引数に配列を指定してください。');
		}

		// キーから値に変換
		foreach ($names as $idx => $name) {
			$names[$idx] = $this->requests[$name];
		}
		// 配列の先頭に追加
		array_unshift($names, $value);

		// 未入力フラグ
		$notInputFlg = TRUE;
		foreach ($names as $idx => $name) {
			if ($name != '') {
				$notInputFlg = FALSE;
			}
			$names[$idx] = trim($name);
		}
		// 全て未入力の場合、TRUEを返す
		if ($notInputFlg) {
			return TRUE;
		}

		for ($ii = 0, $max = count($names); $ii < $max; $ii++) {
			// 範囲外
			if (! isset($names[$ii + 1])) {
				break;
			}

			switch ($operator) {
				case '>':
					if ($names[$ii] <= $names[$ii + 1]) {
						$contradictFlg = TRUE;
						break 2;
					}
					break;
				case '>=':
					if ($names[$ii] < $names[$ii + 1]) {
						$contradictFlg = TRUE;
						break 2;
					}
					break;
				case '==':
					if ($names[$ii] != $names[$ii + 1]) {
						$contradictFlg = TRUE;
						break 2;
					}
					break;
				case '<=':
					if ($names[$ii] > $names[$ii + 1]) {
						$contradictFlg = TRUE;
						break 2;
					}
					break;
				case '<':
					if ($names[$ii] >= $names[$ii + 1]) {
						$contradictFlg = TRUE;
						break 2;
					}
					break;
				default:
					break;
			}
			// 矛盾フラグ
			$contradictFlg = FALSE;
		}
		// 矛盾有り
		if ($contradictFlg) {
			return $message;
		}

		return TRUE;
	}
}
?>