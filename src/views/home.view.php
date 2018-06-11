	<div class="wrapper-content animated fadeInRight ">
		<img class="img-responsive center-block" src="public/img/<?= Config::LOGO_NAME;?>" />
	
		<h2><?= $obj['response'];?></h2>
	
	</div>

    <?php
    // View specific scripts
    array_push($arr_js, '');

    ?>
    <?php
    foreach($arr_js as $js){
        echo '<script src="'.$js.'"></script>';
    }
    ?>

