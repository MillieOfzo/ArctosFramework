<!DOCTYPE html>
<html lang="<?= APP_LANG;?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>/<?= FAVICON_NAME; ?>' />
	
    <title><?= APP_TITLE;?> | Maintenance</title>

	<!-- Mainly CSS -->
	<?php
		foreach(ROOT_CSS as $css){
			echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
		}
	?>

  </head>

<body class="gray-bg"  id="i18container">
<?php
	$img_arr = array(
		'<i class="fa fa-fire"></i>', 
		'<i class="fa fa-code"></i>', 
		'<i class="fa fa-power-off"></i>', 
		'<i class="fa fa-puzzel-piece"></i>', 
		'<i class="fa fa-bomb"></i>', 
		'1337',
		'LIT'
		);
	function getRandomArr($ar){
		$num = array_rand($ar);
		return $ar[$num];
	}
?>
    <div class="middle-box text-center animated fadeInDown">
        <h1 style="color: #ECC37C;"><?= getRandomArr($img_arr);?></h1>
        <h3 class="font-bold" data-i18n="[html]error_page.maintenance.label">Maintenance bezig</h3>

        <div class="error-desc">
		   <p data-i18n="[html]error_page.maintenance.msg">Wacht uit of vraag status na bij de admins.
			</p>	
			<a href="<?= URL_ROOT; ?>" class='btn btn-primary' data-i18n="[html]error_page.return_btn">Return to safety</a>			
        </div>
    </div>

	<!-- Mainly scripts -->
	<?php
		foreach(ROOT_JS as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}
	?>
  </body>
</html>