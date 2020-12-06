<form class="form-signin" role="form" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
	<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
	<?php if($msg): ?>
		<p><?= $msg ?></p>
	<?php endif ?>
	<label for="apikey" class="sr-only">ApiKey</label>
	<input type="password" id="apikey" name="apikey" class="form-control" placeholder="ApiKey" required>

	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>