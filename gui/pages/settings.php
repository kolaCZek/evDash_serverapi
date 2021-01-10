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

		if($gui->setSettings($_SESSION['uid'], $timezone, $notifications)) {
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
	<?php if($msg): ?>
		<p><?= $msg ?></p>
	<?php endif ?>
	<label for="timezone">TimeZone: </label>
	<select name="timezone">
	<?php
		$OptionsArray = timezone_identifiers_list();
        while (list ($key, $row) = each ($OptionsArray) ) {
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

	<button class="btn btn-lg btn-primary btn-block" type="submit">Save</button>
</form>