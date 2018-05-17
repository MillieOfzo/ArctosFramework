<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">

                <div class="logo-element">
					<?= substr(\Config::APP_TITLE,0,3);?>
                </div>
            </li>
            <li><a href="/" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.1"><i class="fa fa-th-large fa-fw"></i> <span class="nav-label"></span></a></li>
			<li><a href="/tickets" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.2"><i class="fa fa-ticket fa-fw"></i> <span class="nav-label"></span></a></li>
            <li><a href="/user" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.4"><i class="fa fa-user fa-fw"></i> <span class="nav-label"></span></a></li>

            <li><br></li>

            <?php if(\App\Classes\Auth::checkAuthUserIsAdmin()){?>
                <li><a href="/users" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.5"><i class="fa fa-users fa-fw c-accent"></i> <span class="nav-label"></span></a></li>
                <li><a href="/logging" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.6"><i class="fa fa-file-text fa-fw c-accent"></i> <span class="nav-label"></span></a></li>
			<?php }; ?>


        </ul>
    </div>

</nav>