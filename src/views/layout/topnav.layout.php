<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary hidden" href="#" id="menu_bar"><i class="fa fa-bars"></i> </a>
            <div class="navbar-form-custom" >
                <div class="form-group">
                    <input type="text" class="form-control autocomplete-append site-nr"  id="top_search" data-i18n="[placeholder]layout.topnav.placeholder" ></input>
                </div>
            </div>
        </div>
        <ul class="nav navbar-top-links navbar-right">
			<li>
				<a ><i class="fa fa-user"></i> <?= @htmlspecialchars($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');?></a>
			</li>
            <li>
                <a href="<?= @URL_ROOT;?>/Src/controllers/login.controller.php?logout&csrf=<?= @$_SESSION['db_token'];?>">
                    <i class="fa fa-sign-out"></i> <span data-i18n="[html]layout.topnav.logout" id="log_out_span"> Log out </span>
                </a>
            </li>
        </ul>
    </nav>
</div>