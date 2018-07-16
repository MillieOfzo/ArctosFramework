<?php

	require_once '../config/bootstrap.php';
	$title = explode('/',$_SERVER['REQUEST_URI']);
	
?>

<!DOCTYPE html>
<html lang="<?= Config::APP_LANG;?>">
<head>

	<title><?= Config::APP_TITLE;?> | <?= $title[1];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="/public/img/<?= Config::FAVICON_NAME;?>" />

    <!-- Mainly CSS -->
    <?php
        foreach($arr_css as $css){
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
	
			<div id="page-wrapper" class="<?= strtolower(\Config::APP_THEME);?>-bg">
	
				<?php
				// Top menu bar
				include '../src/views/layout/topnav.layout.php';

				//var_dump($obj);
                if(isset($obj['response']['view'])) {
                    // First check is to detemine if a class sends back a view
                    include $obj['response']['view'];
                } elseif(file_exists($obj['view'])){
                    // If this is not the case use the controllers view
					include $obj['view'];
				} else {
                    // If none if found return a 404
					http_response_code(404);
					include '../src/views/errors/page_404.view.php';
					die();
				}
								
				// Footer
				include '../src/views/layout/footer.layout.php';
				?>
	
			</div>
	
		</div>
	</body>
    <?php

    if(isset($_SESSION[\Config::SES_NAME]['user_new']) && htmlentities($_SESSION[\Config::SES_NAME]['user_new'], ENT_QUOTES, 'UTF-8') == 1) {
        include '../src/views/modals/new_user.modal.php';
        echo "<script>$('#myModal').modal('show');</script>";
    }
    ?>
<?php 
} else {
    // Check if response view is returned by the login controller
    if(isset($obj['response']['view']))
    {
        include $obj['response']['view'];
    }
    else
    {
        include '../src/views/index.view.php';
    }

    //var_dump($obj);
 };
 ?>

<script type="text/javascript" id="cookieinfo"
        src="/public/js/cookieinfo/cookieinfo.min.js"
        data-bg="#645862"
        data-fg="#FFFFFF"
        data-link="#f6a821"
        data-divlinkbg ="#f6a821"
        data-cookie="ArctosCookie"
        data-text-align="left"
        data-moreinfo ="/cookie">
</script>
<input type="text" hidden id="token" value="<?= isset($_SESSION['_token']) ? htmlspecialchars($_SESSION['_token'], ENT_QUOTES, 'UTF-8') : '';?>" />

</html>
	