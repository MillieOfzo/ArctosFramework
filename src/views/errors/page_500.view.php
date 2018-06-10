<!DOCTYPE html>
<html lang="">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="/public/img/<?= \Config::FAVICON_NAME;?>" />

    <title>500 Error</title>

    <!-- Mainly CSS -->
    <?php
        foreach(\App\Classes\FileManager::cssFiles() as $css){
            echo '<link href="'.$css.'" rel="stylesheet">';
        }
    ?>
	
</head>

<body class="<?= strtolower(\Config::APP_THEME);?>-bg" id="i18container">


    <div class="middle-box text-center animated fadeInDown">
        <h1>500</h1>
        <h3 class="font-bold" data-i18n="[html]error_page.500.label">Internal Server Error</h3>

        <div class="error-desc" >
			<p data-i18n="[html]error_page.500.msg">
            The server encountered something unexpected that didn't allow it to complete the request. We apologize.
            You can go back to main page: 
			</p>
			<br/><a href="/" class='btn btn-primary' data-i18n="[html]error_page.return_btn">Return to safety</a>
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
