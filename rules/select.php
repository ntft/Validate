<?php
/**
 * ルール：選択クラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/13
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 */

// 基底ルールクラスを読み込む
require_once 'base_rule.php';

// 基底クラスを継承
class RuleSelect extends RuleBase
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
	 */
	public function run()
	{
		$min = $max = NULL;
		$values = array();

		if (is_array($this->infos)) {
			// min
			if (array_key_exists('min', $this->infos)) {
				$min = $this->infos['min'];
			}
			// max
			if (array_key_exists('max', $this->infos)) {
				$max = $this->infos['max'];
			}
			// values
			if (array_key_exists('values', $this->infos)) {
				$values = $this->infos['values'];
			}
		}
		else {
			throw new Exception('Rule select : 第3引数には配列を指定してください。');
		}

		if ($min === NULL && $max === NULL) {
			throw new Exception('Rule select : 第3引数の配列には、少なくともmin, maxのどちらかを指定してください。');
		}

		// 選択数を調べる
		$selectedCnt = 0;
		foreach ((array)$this->value as $post) {
			// POSTした値が指定配列の中にあった場合
			if (in_array($post, $values)) {
				$selectedCnt++;
			}
		}

		// minが指定されていて、選択数がminより少ない場合
		if ($min && $min > $selectedCnt) {
			return sprintf($this->inies['not_min'], $min);
		}
		// maxが指定されていて、選択数がmaxより大きい場合
		if ($max && $max < $selectedCnt) {
			return sprintf($this->inies['not_max'], $max);
		}

		return TRUE;
	}
}
?>