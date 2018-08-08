<div class="footer">
    <div class="pull-right">
		<b>Ver</b> <?= Config::APP_ENV." " . Config::APP_VER;?>
    </div>
    <div >
        <b data-i18n="[html]layout.footer.copyright">Copyright</b> <?= \App\Classes\Framework::getFrameWorkCopyright();?> &copy; 2018 <a class="set_en active"><img src="/public/img/flags/16/<?= \App\Classes\Auth::getAuthUserLanguage();?>.png"></a>		
    </div>
</div>