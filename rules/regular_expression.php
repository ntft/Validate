<?php
/**
 * ルール：正規表現クラス
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
class RuleRegularExpression extends RuleBase
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

		// 配列でない場合
		if (! is_array($this->infos)) {
			throw new Exception('Rule regular expression : 第3引数には配列を指定してください。');
		}

		// patternが存在しない
		if (! array_key_exists('pattern', $this->infos)) {
			throw new Exception('Rule regular expression : "pattern"を指定してください。');
		}
		// 存在する
		else {
			$pattern = $this->infos['pattern'];
		}

		// flags, offset(指定のない場合は0、preg_matchのデフォ値)
		$flags = (array_key_exists('flags', $this->infos)) ? $this->infos['flags'] : 0;
		$offset = (array_key_exists('offset', $this->infos)) ? $this->infos['offset'] : 0;
		// マッチしなかった場合のメッセージ
		$message = (array_key_exists('message', $this->infos)) ? $this->infos['message'] : NULL;

		// マッチング
		$ret = preg_match($pattern, $value, $flags, $offset);
		if ($ret === FALSE) {
			throw new Exception('Rule regular expression : preg_match() でエラーが発生しました。パターンを確認してください。');
		}

		// マッチしなかった場合
		$ret = (boolean)$ret;
		if ($ret === FALSE) {
			// メッセージ未指定
			if ($message === NULL) {
				throw new Exception('Rule regular expression : "message"を指定してください。');
			}
			$ret = $message;
		}

		return $ret;
	}
}
?>