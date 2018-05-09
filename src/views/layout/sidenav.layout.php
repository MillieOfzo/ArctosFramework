<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"> <?= @htmlspecialchars($_SESSION[SES_NAME]['user_name'], ENT_QUOTES, 'UTF-8');?></strong>
                         </span> <span class="text-muted text-xs block"> <?= @htmlspecialchars($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');?> <b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="Src/Login/logout.php?csrf=<?= @htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">Logout</a></li>
                        </ul>
                </div>
                <div class="logo-element">
                    ARC
                </div>
            </li>
            <li><a href="<?= Config::APP_ROOT.'/';?>" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.1"><i class="fa fa-th-large fa-fw"></i> <span class="nav-label"></span></a></li>
            <li><a href="<?= Config::APP_ROOT.'/about';?>" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.3"><i class="fa fa-question fa-fw"></i> <span class="nav-label"></span></a></li>
			<li><a href="<?= Config::APP_ROOT.'/user';?>" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.4"><i class="fa fa-user fa-fw"></i> <span class="nav-label"></span></a></li>

			<li><br></li>
			<!--
			<?php if(htmlentities($_SESSION[SES_NAME]['user_role'], ENT_QUOTES, 'UTF-8') == 1){ ?>
            <li><a href="<?= URL_ROOT.'/users/';?>" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.5"><i class="fa fa-users fa-fw"></i> <span class="nav-label"></span></a></li>
            <li><a href="<?= URL_ROOT.'/logging/';?>" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.6"><i class="fa fa-file-text fa-fw"></i> <span class="nav-label"></span></a></li>
            <li><a href="<?= URL_ROOT.'/settings/';?>" data-toggle="tooltip" data-placement="right" data-i18n="[title]layout.sidebar.title.7"><i class="fa fa-gear fa-fw"></i> <span class="nav-label"></span></a></li>
			<?php }; ?>
			-->
        </ul>
    </div>

</nav>