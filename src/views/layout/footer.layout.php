<div class="footer">
    <div class="pull-right">
		<b>Ver</b> <?= Config::getEnv()." " . Config::getVersion();?>
    </div>
    <div >
        <b data-i18n="[html]footer.copyright">Copyright</b> <?= Config::getCopyright();?> &copy; 2018 <a class="set_en active"><img src="public/img/flags/16/<?= Config::APP_LANG;?>.png"></a>
    </div>
</div>