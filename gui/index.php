<?php
	session_start();
	require('gui.class.php');

	$gui = new Gui();
	$signedin = false;

	// Logout
	if(isset($_GET['p']) && $_GET['p'] == "logout") {
		$_SESSION = array();
		session_destroy();
		setcookie('apikey', '', time() - (24 * 3600), '', $_SERVER['SERVER_NAME'], true, true);
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}

	// Login form
	if(isset($_POST['apikey']) && !empty($_POST['apikey'])) {
		if($uid = $gui->signIn($_POST['apikey'])) {
			$_SESSION['uid'] = $uid;
			$_SESSION['apikey'] = $_POST['apikey'];

			if(isset($_POST['rememberme']) && !empty($_POST['rememberme'])) {
				setcookie('apikey', $_POST['apikey'], time() + (10 * 365 * 24 * 60 * 60), '', $_SERVER['SERVER_NAME'], true, true);
			} else {
				if(isset($_COOKIE['apikey'])) {
					setcookie('apikey', '', time() - (24 * 3600), '', $_SERVER['SERVER_NAME'], true, true);
				}
			}

			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		} else {
			$msg = 'Wrong ApiKey - Try again';
		}
	}

	// Is user signed and valid?
	if(isset($_SESSION['apikey']) && isset($_SESSION['uid'])) {
		if($_SESSION['uid'] == $gui->signIn($_SESSION['apikey'])) {
			$signedin = true;
		} else {
			$_SESSION = array();
			session_destroy();
		}
	} elseif(isset($_COOKIE['apikey']) && !empty($_COOKIE['apikey'])) {
		if($uid = $gui->signIn($_COOKIE['apikey'])) {
			$_SESSION['uid'] = $uid;
			$_SESSION['apikey'] = $_COOKIE['apikey'];
			$signedin = true;
		} else {
			setcookie('apikey', '', time() - (24 * 3600), '', $_SERVER['SERVER_NAME'], true, true);
		}
	}

	//Get page
	if(!$signedin) {
		$page = 'login';
	} else {
		if(isset($_GET['p']) && !empty($_GET['p'])) {
			switch ($_GET['p']) {
				case 'status':
					$page = 'status';
					break;
				case 'charging':
					$page = 'charging';
					break;
				case 'settings':
					$page = 'settings';
					break;
				default:
					$page = '404';
					http_response_code(404);
					break;
			}
		} else {
			$page = 'status';
		}
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="robots" content="noindex">
		<meta name="author" content="Martin 'kolaCZerk' Kolací">
		<title>evDash</title>

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/gui.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	</head>
	<body class="text-center">
		<?php if($signedin): ?>
			<nav class="navbar navbar-expand-md navbar-dark bg-dark">
				<a class="navbar-brand" href="#">evDash</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2" id="navbar">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item<?php if($page == 'status'){echo(' active');} ?>">
							<a class="nav-link" href="?p=status">Status</a>
						</li>
						<li class="nav-item<?php if($page == 'charging'){echo(' active');} ?>">
							<a class="nav-link" href="?p=charging">Charging</a>
						</li>
					</ul>

					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
              <a class="nav-link<?php if($page == 'settings'){echo(' active');} ?>" href="?p=settings">Settings</a>
            </li>
						<li class="nav-item">
							<a class="nav-link" href="?p=logout">Sign Out</a>
						</li>
					</ul>
				</div>
			</nav>
		<?php endif ?>
			<?php
				if(file_exists('./pages/'.$page.'.php')) {
					require('./pages/'.$page.'.php');
				}
			?>
	</body>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
	<script>window.jQuery</script>
	<script src="js/bootstrap.bundle.min.js"></script>
</html>
