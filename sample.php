<?php
// バリデートクラスを読み込む
require_once 'validate.php';

// Noticeが出るため設定
error_reporting('E_ALL');

// チェックボックスの値
$checks = array('apple', 'banana', 'chery');

// POSTされてきていた場合
if (array_key_exists('_submit', $_POST) && $_POST['_submit']) {
	// バリデートクラスのオブジェクト生成
	// (バリデートするPOST、またはGET配列を引数にする)
	$validate = new Validate($_POST);

	// ルールの追加
	// 
	// 第1引数：name値
	// 第2引数：ルール名
	// 第3引数：(各ルールによって異なる)

	// [各ルール]
	//
	// 必須チェック					required
	// 数値チェック					numeber
	// 整数チェック					integer
	// 文字数チェック				length
	// バイト数チェック				byte
	// 半角チェック					single
	// 全角チェック					multi
	// ひらがなチェック				hirakana
	// (全角・半角)カタカナチェック	katakana
	// 日付チェック					date
	// 時刻チェック					time
	// メールアドレスチェック		mail
	// 電話番号チェック				tel
	// 郵便番号チェック				zip
	// クレジット番号チェック		credit
	// 正規表現チェック				regular_expression
	// 選択チェック					select
	// 比較チェック					comparison

	// ルール：必須入力
	// (必須入力を追加しなかったname値は未入力でもエラーにはならない)
	$validate->addRule('txt_required', 'required');

	// ルール：数値
	// 第3引数にTRUEを指定すると「PHPの取りうる実数の範囲内かどうか」のチェックも行う
	$validate->addRule('txt_number', 'number');

	// ルール：整数
	// 第3引数にTRUEを指定すると「PHPの取りうる整数の範囲内かどうか」のチェックも行う
	$validate->addRule('txt_integer', 'integer');

	// ルール：文字数
	// 第3引数にキー min, maxを持つ配列を指定する。
	// min：最小値、max：最大値。min, maxのどちらかは指定する必要がある。
	// 単位は「文字」。
	$validate->addRule('txt_length', 'length', array('min' => 5, 'max' => 10));

	// ルール：バイト数
	// 第3引数にキー min, maxを持つ配列を指定する。
	// min：最小値、max：最大値。min, maxのどちらかは指定する必要がある。
	// 単位は「バイト」。
	$validate->addRule('txt_byte', 'byte', array('min' => 5, 'max' => 10));

	// ルール：半角文字
	$validate->addRule('txt_single', 'single');

	// ルール：全角文字
	$validate->addRule('txt_multi', 'multi');

	// ルール：ひらがな
	$validate->addRule('txt_hirakana', 'hiragana');

	// ルール：全角・半角カタカナ	
	// 第3引数は全角フラグ。
	// 何も指定しない場合、全角・半角カタカナを対象。
	// TRUEを指定すると全角カタカナが対象。
	// FALSEを指定すると半角カタカナが対象。
	$validate->addRule('txt_single_katakana', 'katakana', FALSE);

	// 第3引数にTRUEを指定しているので全角カタカナをチェック。
	$validate->addRule('txt_multi_katakana', 'katakana', TRUE);

	// ルール：日付
	// 第3引数にキー delimiter, dayを持つ配列を指定する。
	// delimiter：区切り文字、day：日有り(省略可、省略時はTRUE)
	// 例) 日有り - 2011/10/19
	//     日無し - 2011/10
	$validate->addRule('txt_date', 'date', array('delimiter' => '/', 'day' => TRUE));

	// ルール：時刻
	// 第3引数にキー delimiter, secondを持つ配列を指定する。
	// delimiter：区切り文字、second：秒有り(省略可、省略時はTRUE)
	// 例) 秒有り - 3:40:56
	//     秒無し - 3:40
	$validate->addRule('txt_time', 'time', array('delimiter' => ':', 'second' => TRUE));

	// ルール：メールアドレス
	$validate->addRule('txt_mail', 'mail');

	// ルール：電話番号
	// 第3引数は固定電話フラグ。
	// 指定しないと固定・携帯電話番号のどちらも対象。
	// TRUEにすると固定電話番号のみ対象。
	// FALSEにすると携帯電話番号のみ対象。
	// (※ハイフンで番号を区切ること)
	$validate->addRule('txt_tel', 'tel');

	// ルール：郵便番号
	// 第3引数は郵便番号存在フラグ。
	// 第3引数にTRUEを指定すると、郵便番号が存在するかもチェックする。
	$validate->addRule('txt_zip', 'zip');

	// ルール：クレジットカード番号
	// 以下のルールに基づいてチェックしている。
	// クレジットカードのチェックディジット - すばらしき functional programming - haskell
	// http://haskell.g.hatena.ne.jp/masshie/20080714
	// オーソリは行なっていないことに注意
	// (※ハイフンや半角スペースで区切られていても良い)
	$validate->addRule('txt_credit', 'credit');

	// ルール：正規表現
	//
	// 第3引数にキー pattern, message, flags(省略可), offset(省略可)をキーに持つ配列を指定する。
	// pattern	：preg_machの第1引数
	// message	：パターンにマッチしなかった場合のエラーメッセージ
	// flags	：preg_machの第4引数
	// offset	：preg_machの第5引数
	// PHP: preg_match - Manual
	// http://jp2.php.net/manual/ja/function.preg-match.php
	$validate->addRule('txt_regular_expression', 'regular_expression',
					  array('pattern' => '/^\d+$/', 'message' => 'パターン「/^\d+$/」にマッチしません。'));

	// ルール：選択
	// 第3引数にキー values, min, maxを配列を指定する。
	// values：選択できる全ての値(配列)
	// min：選択の最小値
	// max：選択の最大値
	//
	// valuesが「array('apple', 'banana', 'chery')」で、
	// 選択した値が「apple」で、minが「1」の場合、
	// 「必ず1つは選択しなければならない」ということになる。
	// チェックボックス、ラジオボタンで使用する
	$validate->addRule('check', 'select', array('values' => $checks, 'min' => 1, 'max' => 1));

	// ルール：比較
	// 第3引数にキー names, operator, messageを持つ配列を指定する。
	// names	：第1引数に指定した値と比較する値(配列)
	// operator	：比較演算子( >, >=. ==, <=, < )
	// message	：比較に矛盾した場合のメッセージ
	// ※比較は指定したoperatorによって行う
	// 
	// 第1引数にtxt_comp1を指定し、第3引数のキーnamesにtxt_comp2, txt_comp3を指定し、
	// operatorに <= を指定した場合、
	// 「txt_comp1 <= txt_comp2 <= txt_comp3」とならないとエラーメッセージが表示される。
	$validate->addRule('txt_comp1', 'comparison',
					   array('names' => array('txt_comp2'), 'operator' => '<',
							 'message' => '開始日は終了日以前で入力してください。')
	);

	// 1つのname値に対して複数のルールを追加することが可能
	$validate->addRule('txt_comp1', 'date', array('delimiter' => '/', 'day' => TRUE));
	$validate->addRule('txt_comp2', 'date', array('delimiter' => '/', 'day' => TRUE));

	// あるname値に登録されているルールをコピーする
	// ただし、comparison(ルール：比較)は除く
	$validate->copyRule('txt_comp2','txt_comp1');

	/*
	// ルールを消すことも可能
	//
	// txt_comp2のルールを全て消す
	$validate->delRule('txt_comp2');
	// txt_comp2のルールdateのみ削除
	$validate->delRule('txt_comp2', 'date');
	*/

	// バリデート実行
	$validate->run();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>バリデーションクラスのサンプル</title>

<style type="text/css">
dt, dd {
	width: 400px;
	margin: 0;
	padding: 0;
}
dt {
	font-weight: bold;
}
dd {
	margin-bottom: 15px;
}
dd span {
	color: red;
}
dd input {
	width: 300px;
}
</style>
</head>

<body>

<h2>バリデーションクラスのサンプル</h2>

<!--
$validate->results には、Validateクラスのコンストラクタに指定した配列に、
trim()、htmlspecialchars()した値が格納されている。
 -->
<form name="frm" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<dl>
	<dt>必須入力チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_required'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_required" value="<?php echo $validate->results['txt_required'] ?>" />
	</dd>
	<!-- -->
	<dt>数値チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_number'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_number" value="<?php echo $validate->results['txt_number'] ?>" />
	</dd>
	<!-- -->
	<dt>整数チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_integer'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_integer" value="<?php echo $validate->results['txt_integer'] ?>" />
	</dd>
	<!-- -->
	<dt>文字数チェック(5文字から10文字まで)</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_length'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_length" value="<?php echo $validate->results['txt_length'] ?>" />
	</dd>
	<!-- -->
	<dt>バイト数チェック(5バイトから10バイトまで)</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_byte'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_byte" value="<?php echo $validate->results['txt_byte'] ?>" />
	</dd>
	<!-- -->
	<dt>半角チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_single'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_single" value="<?php echo $validate->results['txt_single'] ?>" />
	</dd>
	<!-- -->
	<dt>全角チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_multi'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_multi" value="<?php echo $validate->results['txt_multi'] ?>" />
	</dd>
	<!-- -->
	<dt>ひらがなチェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_hirakana'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_hirakana" value="<?php echo $validate->results['txt_hirakana'] ?>" />
	</dd>
	<!-- -->
	<dt>半角カタカナチェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_single_katakana'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_single_katakana" value="<?php echo $validate->results['txt_single_katakana'] ?>" />
	</dd>
	<!-- -->
	<dt>全角カタカナチェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_multi_katakana'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_multi_katakana" value="<?php echo $validate->results['txt_multi_katakana'] ?>" />
	</dd>
	<!-- -->
	<dt>日付チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_date'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_date" value="<?php echo $validate->results['txt_date'] ?>" />
	</dd>
	<!-- -->
	<dt>時刻チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_time'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_time" value="<?php echo $validate->results['txt_time'] ?>" />
	</dd>
	<!-- -->
	<dt>メールアドレスチェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_mail'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_mail" value="<?php echo $validate->results['txt_mail'] ?>" />
	</dd>
	<!-- -->
	<dt>電話番号チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_tel'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_tel" value="<?php echo $validate->results['txt_tel'] ?>" />
	</dd>
	<!-- -->
	<dt>郵便番号チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_zip'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_zip" value="<?php echo $validate->results['txt_zip'] ?>" />
	</dd>
	<!-- -->
	<dt>クレジット番号チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_credit'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_credit" value="<?php echo $validate->results['txt_credit'] ?>" />
	</dd>
	<!-- -->
	<dt>正規表現チェック(/^\d+$/)</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_regular_expression'] as $error) echo "<span>$error</span><br />"; ?>
	<input type="text" name="txt_regular_expression" value="<?php echo $validate->results['txt_regular_expression'] ?>" />
	</dd>
	<!-- -->
	<dt>選択チェック</dt>
	<dd>
	<?php
	foreach ((array)$validate->errors['check'] as $error) echo "<span>$error</span><br />";
	foreach ((array)$checks as $check) {
		if (in_array($check, $validate->results['check'])) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
		echo '<input type="checkbox" name="check[]" value="' . $check . '" ' . $checked . ' /><br />' . PHP_EOL;
	}
	?>
	</dd>
	<!-- -->
	<dt>比較チェック</dt>
	<dd>
	<?php foreach ((array)$validate->errors['txt_comp1'] as $error) echo "<span>$error</span><br />"; ?>
	開始日：<input type="text" name="txt_comp1" value="<?php echo $validate->results['txt_comp1'] ?>" /><br />
	<?php foreach ((array)$validate->errors['txt_comp2'] as $error) echo "<span>$error</span><br />"; ?>
	終了日：<input type="text" name="txt_comp2" value="<?php echo $validate->results['txt_comp2'] ?>" />
	</dd>
	<!-- -->
	<input type="submit" />
	<input type="hidden" name="_submit" value="TRUE" />
</dl>
</form>

</body>
</html>