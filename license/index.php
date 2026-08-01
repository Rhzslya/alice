<?php
require_once 'config/config.php';
require_once 'class/Core.php';

$isCompleted = FALSE;
$core = new Core;
$check = $core->init($config);

if ($_POST){
$core->setInput($_POST);

	if ($core->reWrite()) $isCompleted = TRUE;

	///redirect ::::
	header('Location: ../login');
}
	



$drive = 'c';
if (preg_match('#Volume Serial Number is (.*)\n#i', shell_exec('dir '.$drive.':'), $m)) {
	$volname = $m[1];
} else {
	$volname = '';
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>License Activation</title>
	<link rel="stylesheet" type="text/css" href="../assets/css/license.css">
</head>
<body>
<div class="wrapper-license">	
	<div class="license-header">
		License Warning
	</div>

	<div class="license-icon"></div>
	<div class="license-form">
		<form id="form_install" action="" method="POST">
		<table>
			<tr>
				<td width="150">Your Key</td>
				<td class="the-key">
					<?=$volname?>
				</td>
			</tr>
			<tr>
				<td>Enter License Code</td>
				<td><input type="text" name="license" size="30" required="" autofocus="" autocomplete="off"></td>
			</tr>
		</table>
		<div class="license-warning">Ask your vendor to get new license, please.</div>
		<input type="submit" value="Register">
		</form>

	</div>
</div>

</body>
</html>
