<?php
/**
 * ルール：郵便番号クラス
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
class RuleZip extends RuleBase
{
	const ZIP_FILE = 'zip/Zip.php';
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

		// 郵便番号形式ではない場合
		if (! preg_match('/^\d{3}-?\d{4}$/', $value)) {
			return $this->inies['not_zip'];
		}

		// 郵便番号クラスファイルのパス
		$zipPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::ZIP_FILE;

		// 第3引数がTRUE、ファイルが存在する場合
		if ($this->infos === TRUE && file_exists($zipPath)) {
			// 郵便番号クラスを読み込む
			require_once($zipPath);
			$objects = Zip::search($value);
			// 郵便番号が存在した場合
			if ((int)$objects[0]->result_values_count > 0) {
				return TRUE;
			}
			// 存在しない
			else {
				return $this->inies['not_exist_zip'];
			}
		}
		return TRUE;
	}
}
?>