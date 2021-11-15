<?php

// <script>alert("がははははは！")</script>
// neko@example.com
// $val = filter_input(INPUT_POST, 'val');
// $val = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_SPECIAL_CHARS);
// $val = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// $val = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_STRING);

// $val = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_STRING);
// echo $val . PHP_EOL;
// $val = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_EMAIL);
// echo $val . PHP_EOL;
// $val = filter_input(INPUT_POST, 'val', FILTER_VALIDATE_EMAIL);
// echo $val . PHP_EOL;
// $val = filter_var($val, FILTER_VALIDATE_EMAIL);
// echo $val . PHP_EOL;

// $val = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_NUMBER_INT);
// var_dump($val);

// $ary = filter_input(INPUT_POST, 'ary', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// foreach ($ary as $v) {
// 	echo $v . PHP_EOL;
// }

// $opts = array(
// 	'options' => array(
// 		'min_range' => 0,
// 		'max_range' => 100,
// 		// 'default' => 50
// 	),
// 	'flags' => FILTER_NULL_ON_FAILURE
// );

// $val = filter_input(INPUT_POST, 'val', FILTER_VALIDATE_INT, $opts);
// echo $val . PHP_EOL;

function valid_tel($tel)
{
	$tel = trim($tel);
	if (preg_match('/\A\(?\d{2,5}\)?[-(\.\s)]{0,2}\d{1,4}[-(\.\s)]{0,2}\d{3,4}\z/u', $tel)) {
		return $tel;
	} else {
		return false;
	}
}

$opts = [
	'options' => 'valid_tel'
];

$val = filter_input(INPUT_POST, 'val', FILTER_CALLBACK, $opts);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
	<div class="container">
		<div class="row my-5">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="car-header">
					<? var_dump($val) ?>
					<!-- <? var_dump($ary) ?> -->
				</div>
				<div class="card-body">
					<form action="./test.php" method="post">
						<input type="hidden" name="ary[]" value="1">
						<input type="hidden" name="ary[]" value="2">
						<input type="hidden" name="ary[]" value="3">
						<div class="form-group">
							<input type="text" name="val" id="val" class="form-control">
						</div>
						<input type="submit" value="送信" class="btn btn-primary">
					</form>
				</div>
			</div>
			<div class="col-md-4"></div>
		</div>
	</div>
</body>

</html>
