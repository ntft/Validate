<?php
/**
 * バリデートクラス
 *
 * @version 1.0.0
 * @charset UTF-8
 * @created 2011/10/05
 * @modified 2011/10/19
 * @author ntft
 * @copyright ntft
 * @license MIT License
 * @caution PHP 5.0 以上必須
 */

class Validate
{
	public	$errors;		// エラー配列
	public	$results;		// trim, htmlspecialchars後の$requests配列
	// ---
	private $ruleDir;		// rulesディレクトリ
	private	$requests;		// POST or GET配列(以下、リクエスト配列)
	private	$rules;			// ルール配列
	private $validateTypes;	// バリデーション種別配列

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param array $posts リクエスト配列
	 * @return void
	 */
	public function __construct($requests)
	{
		$this->ruleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rules' . DIRECTORY_SEPARATOR;
		// ruleディレクトリ内からPHPファイルを取得する
		// ※base_rule.phpは対象外
		$this->validateTypes = $this->_getPHPFile($this->ruleDir);

		$this->requests = $requests;
	}

	/**
	 * ルールを追加する
	 *
	 * @access public
	 * @param 可変長引数(name, type [, etc [, etc...]])
	 * @return boolean TRUE(OK) / FALSE(NG)
	 */
	public function addRule()
	{
		try {
			// 引数が指定数より少ない場合
			if (($num = func_num_args()) < 2) {
				throw new Exception('引数の数が規定より少ないです : ' . $num);
			}

			// 引数の値を取得
			$args = func_get_args();
			// name値
			$name = $args[0];
			// バリデーション種別
			$type = $args[1];

			// 未定義バリデーション種別を指定された場合
			if (! in_array($type, $this->validateTypes)) {
				throw new Exception('未定義のバリデーション種別です : ' . $type);
			}

			// name値が存在した場合
			if (isset($this->rules[$name])) {
				$rules = $this->rules[$name];
			}
			// 存在しない場合
			else {
				$rules = array();
			}
			// name, typeを除いた配列値
			$rules[$type] = array_slice($args, 2);
			// 今回分を追加
			$this->rules[$name] = $rules;

			return TRUE;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return FALSE;
	}

	/**
	 * ルールを削除する
	 *
	 * @access public
	 * @param string $name name値
	 * @param string $type ルール名
	 * @return boolean TRUE(OK) / FALSE(NG)
	 */
	public function delRule($name, $type = NULL)
	{
		// ルールにname値が存在しない場合
		if (! array_key_exists($name, (array)$this->rules)) {
			return FALSE;
		}
		// 種別指定なし
		if ($type === NULL) {
			// name値ごと削除
			unset($this->rules[$name]);
			return TRUE;
		}

		// ルールにtype値が存在しない場合
		if (! array_key_exists($type, (array)$this->rules[$name])) {
			return FALSE;
		}
		unset($this->rules[$name][$type]);
		return TRUE;
	}

	/**
	 * ルールをコピーする
	 *
	 * @access public
	 * @param string $destName コピー先のname値
	 * @param string $srcName コピー元のname値
	 * @return boolean TRUE(OK) / FALSE(NG)
	 */
	public function copyRule($destName, $srcName)
	{
		// コピー元のname値が存在しない場合
		if (! array_key_exists($srcName, (array)$this->rules)) {
			return FALSE;
		}

		// ルールのコピー
		foreach ((array)$this->rules[$srcName] as $idx => $rule) {
			// comparison：選択チェックは除く
			if ($idx == 'comparison') {
				continue;
			}
			$this->rules[$destName][$idx] = $rule;
		}
		return TRUE;
	}

	/**
	 * バリデーションを実行する
	 *
	 * @access public
	 * @return boolean TRUE(エラー無し) / FALSE(エラー有り)
	 */
	public function run()
	{
		try {
			// ルールが無い場合
			if (count($this->rules) === 0) {
				throw new Exception('Validate::run : ルールがありません、追加してください。');
			}

			// name値でループ
			foreach ((array)$this->rules as $name => $rules) {
				// ruleでループ
				foreach ((array)$rules as $ruleNm => $rule) {
					// ルールオブジェクトを作成
					$objRule = $this->_factory($ruleNm);
					// リクエスト配列を渡す
					$objRule->requests = $this->requests;
					// バリデーション実行準備
					$objRule->prepara($this->requests[$name], $rule);
					// バリデーション実行
					$errMsg = $objRule->run();

					// エラー有り
					if ($errMsg !== TRUE) {
						$this->errors[$name][] = $errMsg;
						$this->isError = TRUE;
					}
				}
			}

			// results配列に代入
			foreach ((array)$this->requests as $idx => $request) {
				if (is_array($request)) {
					foreach ($request as $reqIdx => $req) {
						$this->results[$idx][$reqIdx] = htmlspecialchars(trim($req));
					}
				} else {
					$this->results[$idx] = htmlspecialchars(trim($request));
				}
			}

			return (boolean)count($this->errors);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return FALSE;
	}

	/**
	 * 指定ディレクトリからPHPファイル名を取得する
	 *
	 * @access private
	 * @param string $dir ディレクトリ
	 * @return array ファイル配列
	 */
	private function _getPHPFile($dir)
	{
		try {
			// 存在確認
			if (! file_exists($dir)) {
				throw new Exception('ディレクトリ（' . $dir . '）が存在しません。');
			}
			// 読み取り確認
			if (! is_readable($dir)) {
				throw new Exception('ディレクトリ（' . $dir . '）は読み込めません。');
			}

			// ディレクトリハンドルをオープン
			if (($resDir = opendir($dir)) === FALSE) {
				throw new Exception('ディレクトリハンドルのオープンに失敗しました。');
			}
			$files = array();
			while ($file = readdir($resDir)) {
				// 読み取りに失敗
				if ($file === FALSE) {
					throw new Exception('エントリの読み込みに失敗しました。');
				}
				// 除外PHPファイル
				if ($file === 'base_rule.php') {
					continue;
				}
				// ファイル名の末尾が「.php」以外の場合
				if (! preg_match('/(.+?)\.php$/i', $file, $matches)) {
					continue;
				}
				$files[] = $matches[1];
			}
			// ハンドルをクローズ
			closedir($resDir);

			return $files;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return FALSE;
	}

	/**
	 * factoryメソッド
	 *
	 * @access private
	 * @param string $rule ルール名
	 * @return object ルールオブジェクト(OK) / FALSE(NG)
	 */
	private function _factory($rule)
	{
		try {
			$filePath = $this->ruleDir . $rule . '.php';
			// 存在確認
			if (! file_exists($filePath)) {
				throw new Exception('ディレクトリ(' . $filePath . ')が存在しません。');
			}
			// 読み取り確認
			if (! is_readable($filePath)) {
				throw new Exception('ディレクトリ(' . $filePath . ')は読み込めません。');
			}
			// 読み込む
			require_once($filePath);

			// キャメルケースのクラス名を取得
			$clsNm = 'Rule' . $this->_snakeToCamel($rule);

			return (new $clsNm);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return FALSE;
	}

	/**
	 * スネークケースをキャメルケースに変換する
	 *
	 * @access private
	 * @param string $snake スネークケース文字列
	 * @return string キャメルケース文字列
	 */
	private function _snakeToCamel($snake)
	{
		// アンダースコアで分割
		$tmps = explode('_', $snake);
		$camel = '';
		foreach ($tmps as $val) {
			$camel .= ucfirst($val);
		}
		return $camel;
	}
}
?>