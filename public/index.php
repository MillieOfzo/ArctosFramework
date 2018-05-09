<?php

	require '../config/bootstrap.php';

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
	<link rel='shortcut icon' type='image/x-icon' href="public/img/<?= Config::FAVICON_NAME;?>" />

    <!-- Mainly CSS -->
    <?php
        foreach(\App\Classes\Package::cssPackage() as $css){
            echo '<link href="'.$css.'" rel="stylesheet">';
        }
    ?>

</head>

<body class="mini-navbar" id="i18container">

    <div id="wrapper">

        <?php include '../src/views/layout/sidenav.layout.php';?>

        <div id="page-wrapper" class="gray-bg">

            <?php
            // Top menu bar
            include '../src/views/layout/topnav.layout.php';
            \App\Classes\Exceptions::getEx();
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

    <!-- Mainly scripts -->
    <?php
        foreach(\App\Classes\Package::jsPackage() as $js){
            echo '<script src="'.$js.'"></script>';
        }
    ?>

</body>
</html>
	