<?php

	require_once '../config/bootstrap.php';

?>

<!DOCTYPE html>
<html lang="<?= Config::APP_LANG;?>">
<head>

	<title><?= Config::APP_TITLE;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/x-icon' href="/public/img/<?= Config::FAVICON_NAME;?>" />

    <!-- Mainly CSS -->
    <?php
        foreach(\App\Classes\Package::cssPackage() as $css){
            echo '<link href="'.$css.'" rel="stylesheet">';
        }
    ?>

</head>

<?php
if (\App\Classes\Auth::checkAuth()){
?>
	<body class="mini-navbar" id="i18container">
	
		<div id="wrapper">
	
			<?php include '../src/views/layout/sidenav.layout.php';?>
	
			<div id="page-wrapper" class="gray-bg">
	
				<?php
				// Top menu bar
				include '../src/views/layout/topnav.layout.php';
	
				// View content
				if(file_exists($content)){
					include $content;
				} else {
					http_response_code(404);
					include '../src/views/errors/page_404.php';
					die();
				}
	
				// Footer
				include '../src/views/layout/footer.layout.php';
				?>
	
			</div>
	
		</div>
	
	</body>
<?php } else { ?>
	<body class="gray-bg" id="i18container">
	
		<div class="middle-box text-center loginscreen animated fadeInDown ">
			<div class="wrapper wrapper-content">
				<div>
					<!--<h1 class="logo-name">DB+</h1>-->
					<h1 class="logo-name"><img src="/public/img/<?= \Config::LOGO_NAME;?>" width="100%"></img></h1>
				</div>
					<h3 ><span data-i18n="[html]loginscreen.welcome">Welcome to</span> <?= \Config::APP_TITLE;?> </h3>
					<p data-i18n="[html]loginscreen.text">An improved experience for managing RMS and SCS.</p>
					<p data-i18n="[html]loginscreen.subtext">Login in. To see it in action.</p>
	
				
				<form class="m-t" id="signinForm" action="/login" method="post">
					<div class="form-group">
						<input type="email" class="form-control" placeholder="Email" name="email" required="" value="" data-i18n="[placeholder]loginscreen.placeholder.email">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" placeholder="Password" name="password" required="" data-i18n="[placeholder]loginscreen.placeholder.password">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b" name="login" value="Login" data-i18n="[html]loginscreen.login">Login</button>
	
					<a class="link showform" id="password"><small data-i18n="[html]loginscreen.forget">Forgot password?</small></a>
					
					<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['_token'], ENT_QUOTES, 'UTF-8');?>">
				</form>
			
				<div id="show_form" style="display: none;">
					<form class="login-form" action="/login/gentoken" method="post">
						<div class="form-group">
							<input type="email" class="form-control" placeholder="Email" name="email" required="" data-i18n="[placeholder]loginscreen.placeholder.email">
						</div>			
						<button type="submit" name="request" value="request" class="btn btn-primary block full-width m-b" data-i18n="[html]loginscreen.request">Request </button>
	
						<a class="link showform" id="password" data-i18n="[html]loginscreen.login">Login</a>
						<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['_token'], ENT_QUOTES, 'UTF-8');?>">
					</form>
				</div>	
	
				<p class="m-t"> <small><?= date("D d-m-Y"). "<font color='#0092D0'> | </font>". date("H:i:s")."<font color='#0092D0'> | </font> ". \Config::GetEnv() . " " . \Config::getVersion(); ?></small> </p>
				
			</div>
		</div>		
	</body>
	
	
<?php }; ?>
		
    <!-- Mainly scripts -->
    <?php
        foreach(\App\Classes\Package::jsPackage() as $js){
            echo '<script src="'.$js.'"></script>';
        }
    ?>
</html>
	