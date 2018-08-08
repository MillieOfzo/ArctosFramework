<div class="row">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        
		<div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2" href="#" id="menu_bar"><i class="stroke-hamburgermenu"></i> </a>

            <div class="navbar-form-custom" >
                <div class="form-group">
                    <input type="text" class="form-control autocomplete-append site-nr"  id="top_search" data-i18n="[placeholder]layout.topnav.placeholder" ></input>
                </div>
            </div>
        </div>
        <ul class="nav navbar-top-links navbar-right pull-right">
            <li>
                <a style="pointer:none;"><i class="fa fa-user"></i> <span id="user_span"><?= htmlspecialchars($_SESSION[\Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');?></span></a>
            </li>
            <li>
                <a href="/logout/<?= $_SESSION['_token'];?>">
                    <i class="fa fa-sign-out"></i> <span data-i18n="[html]layout.topnav.logout" id="log_out_span"> Log out </span>
                </a>
            </li>
        </ul>

    </nav>
</div>