<?php
/**
 * 基底ルールクラス
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

abstract class RuleBase
{
	protected	$value;		// 値
	protected	$infos;		// prepara()に指定した引数
	protected	$inies;		// INIファイル
	public		$requests;	// リクエスト配列

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($fileNm)
	{
		// 「./../message.ini」
		$iniPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'message.ini';

		// INIファイルの読み込み
		$this->inies = parse_ini_file($iniPath, TRUE);

		// iniファイルからエラーメッセージを取得
		$fileNm = str_replace('.php', '', basename($fileNm));
		if (array_key_exists($fileNm, $this->inies)) {
			$this->inies = $this->inies[$fileNm];
		}
	}

	/**
	 * バリデーション準備
	 *
	 * @access public
	 * @param array 可変長引数
	 * @return void
	 */
	public function prepara()
	{
		$args = func_get_args();
		// 0番目：値
		$this->value = $args[0];
		// 1番目以降：オプション値
		$this->infos = array_slice($args, 1);

		if (array_key_exists(0, $this->infos)) {
			$this->infos = $this->infos[0];
		}
		if (array_key_exists(0, $this->infos)) {
			$this->infos = $this->infos[0];
		}
	}

	/**
	 * バリデートする
	 *
	 * @access public
	 * @memo 抽象メソッド
	 */
	abstract public function run();
}
?>