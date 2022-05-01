<?php
	if(isset($_POST) && !empty($_POST)) {
		if(isset($_POST['timezone']) && !empty($_POST['timezone'])) {
			$timezone = $_POST['timezone'];
		} else {
			$timezone = 'UTC';
		}
		if(isset($_POST['notifications']) && !empty($_POST['notifications'])) {
			$notifications = true;
		} else {
			$notifications = false;
		}
		if(isset($_POST['abrp_enabled']) && !empty($_POST['abrp_enabled'])) {
			$abrp_enabled = true;
		} else {
			$abrp_enabled = false;
		}
		if(isset($_POST['abrp_token']) && !empty($_POST['abrp_token'])) {
			$abrp_token = $_POST['abrp_token'];
		} else {
			$abrp_token = '';
		}

		if($gui->setSettings($_SESSION['uid'], $timezone, $notifications, $abrp_enabled, $abrp_token)) {
			header('Location: '.$_SERVER['REQUEST_URI']);
		} else {
			$msg = 'Settings not saved :(';
		}
	}

	$dta = $gui->getSettings($_SESSION['uid']);

	if(empty($dta->timezone)) {
		$dta->timezone = 'UTC';
	}
?>

<form class="form-settings" role="form" method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
	<h1 class="h3 mb-3 font-weight-normal">Settings</h1>
	<?php if(isset($msg)): ?>
		<p><?= $msg ?></p>
	<?php endif ?>
	<label for="timezone">TimeZone: </label>
	<select name="timezone">
	<?php
		$OptionsArray = DateTimeZone::listIdentifiers();
#        while (list ($key, $row) = foreach ($OptionsArray) ) {
#	while has been deprecated in PHP8 - updated to https://www.php.net/manual/en/control-structures.foreach.php
		foreach ($OptionsArray as $row) {
            $option ='<option value="'.$row.'"';
            $option .= ($row == $dta->timezone ? ' selected' : '');
            $option .= '>'.$row.'</option>';
            echo($option);
        }
    ?>
	</select>
	<div class="checkbox mb-3">
		<label>
			Notifications <input type="checkbox" name="notifications" value="notifications"<?php if($dta->notifications) {echo(' checked="checked"');}?>>
		</label>
	</div>
	<label>ABRP Token: </label>
	<input type="input" name="abrp_token" value="<?= $dta->abrp_token ?>">
	<div class="checkbox mb-3">
		<label>
			Send to ABRP <input type="checkbox" name="abrp_enabled" value="abrp_enabled"<?php if($dta->abrp_enabled) {echo(' checked="checked"');}?>>
		</label>
	</div>

	<button class="btn btn-lg btn-primary btn-block" type="submit">Save</button>
</form>
