<!DOCTYPE html>
<html lang="">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="/public/img/<?= \Config::FAVICON_NAME;?>" />

    <title> 403 Error</title>

    <!-- Mainly CSS -->
    <?php
        foreach(\App\Classes\Package::cssPackage() as $css){
            echo '<link href="'.$css.'" rel="stylesheet">';
        }
    ?>

</head>

<body class="gray-bg"  id="i18container">


    <div class="middle-box text-center animated fadeInDown">
        <h1>403</h1>
        <h3 class="font-bold" data-i18n="[html]error_page.403.label">Access denied</h3>

        <div class="error-desc">
           <p data-i18n="[html]error_page.403.msg">
			Admin rechten zijn verplicht voor toegang. Neem contact op met de admins.
            </p>
			<a href="/" class='btn btn-primary' data-i18n="[html]error_page.return_btn">Return to safety</a>
        </div>
    </div>

    <!-- Mainly scripts -->
    <?php
        foreach(\App\Classes\Package::jsPackage()  as $js){
            echo '<script src="'.$js.'"></script>';
        }	
    ?>

</body>

</html>
