<!DOCTYPE html>
<html lang="">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="/public/img/<?= \Config::FAVICON_NAME;?>" />

    <title>Maintenance</title>

    <!-- Mainly CSS -->
    <?php
        foreach(\App\Classes\FileManager::cssFiles() as $css){
            echo '<link href="'.$css.'" rel="stylesheet">';
        }
    ?>
	
</head>

<body class="<?= strtolower(\Config::APP_THEME);?>-bg" id="i18container">
    <div class="middle-box text-center animated fadeInDown">
        <img src="/public/img/support-maintenance.png" style="width:100%;"/>
        <h3 class="font-bold" data-i18n="[html]error_page.maintenance.label">Under construction</h3>

        <div class="error-desc">
		   <p data-i18n="[html]error_page.maintenance.msg">
		   This page is currently undergoing scheduled maintenance. You will soon be able to get started. Thank you for your patience
			</p>	
			<a href="/" class='btn btn-accent' data-i18n="[html]buttons.go_back">Return to safety</a>
        </div>
    </div>

    <!-- Mainly scripts -->
    <?php
        foreach(\App\Classes\FileManager::jsFiles()  as $js){
            echo '<script src="'.$js.'"></script>';
        }	
    ?>
  </body>
</html>