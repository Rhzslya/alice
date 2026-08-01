<!-- <!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<h1>LICENSE WARNING!</h1>
<h3>NO LICENSE</h3>
<br />
<a href="<?=base_url('license/get_serial')?>">Get License</a>
</body>
</html> -->

<!DOCTYPE html>
<html>
<head>
	<title>License Activation</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/license.css')?>">
</head>
<body>
<div class="wrapper-license">	
	<div class="license-header">
		License Warning
	</div>

	<div class="license-icon"></div>
	<div class="license-form">
		<?=form_open('license/registration')?>
		<table>
			<tr>
				<td width="150">Your Key</td>
				<td class="the-key">
					<?=$vol?>
				</td>
			</tr>
			<tr>
				<td>Enter License Code</td>
				<td><input type="text" name="f_lic_code" size="30" required="" autofocus=""></td>
			</tr>
		</table>
		<div class="license-warning">Ask your vendor to get new license, please.</div>
		<input type="submit" value="Register">
	

	</div>
</div>

</body>
</html>
