
<!DOCTYPE html>
<html lang="<?= APP_LANG;?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>/<?= FAVICON_NAME; ?>' />
	
    <title><?= APP_TITLE;?> | Denied</title>

	<!-- Mainly CSS -->
	<?php
		foreach(ROOT_CSS as $css){
			echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
		}
	?>

  </head>

<body class="gray-bg"  id="i18container">

    <div class="middle-box text-center animated fadeInDown">
        <h1 class="text-danger">X</h1>
        <h3 class="font-bold" data-i18n="[html]error_page.denied.label">Toegang geweigerd</h3>

        <div class="error-desc">
			<p data-i18n="[html]error_page.denied.msg">Denk je dat je hier iets te zoeken hebt? Vraag dan na bij een admin waarom je geen toegang hebt.
			</p>
			
			<a href="<?= URL_ROOT; ?>" class='btn btn-primary' data-i18n="[html]error_page.return_btn" >Return to safety</a>
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