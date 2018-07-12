	<body class="<?= strtolower(\Config::APP_THEME);?>-bg" id="i18container">
	
		<div class="middle-box text-center loginscreen animated fadeInDown ">
			<div class="wrapper wrapper-content">

				<h1 class="logo-name"><img src="/public/img/<?= \Config::LOGO_NAME;?>" class="header-img" width="100%"></img></h1>			
				<h3 ><span data-i18n="[html]loginscreen.welcome">Welcome to</span> <?= \Config::APP_TITLE;?> </h3>
				<p data-i18n="[html]loginscreen.text">An improved experience for managing RMS and SCS.</p>
				<p data-i18n="[html]loginscreen.subtext">Login in. To see it in action.</p>
				
				<?= $obj['response'];?>
				
				<form class="m-t" id="signinForm" action="<?php if(\Config::LDAP_ENABLED){echo'/loginldap';} else { echo '/login';}?>" method="post">
					<div class="form-group">
						<input type="email" class="form-control" placeholder="Email" name="email" required="" value="" data-i18n="[placeholder]loginscreen.placeholder.email">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" placeholder="Password" name="password" required="" data-i18n="[placeholder]loginscreen.placeholder.password">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b" name="login" value="Login" data-i18n="[html]loginscreen.login">Login</button>
	
					<a class="link showform" id="password"><small data-i18n="[html]loginscreen.forget">Forgot password?</small></a>

				</form>
			
				<div id="show_form" style="display: none;">
					<form class="login-form" action="/login/gentoken" method="post">
						<div class="form-group">
							<input type="email" class="form-control" placeholder="Email" name="email" required="" data-i18n="[placeholder]loginscreen.placeholder.email">
						</div>			
						<button type="submit" name="request" value="request" class="btn btn-primary block full-width m-b" data-i18n="[html]loginscreen.request">Request </button>
	
						<a class="link showform" id="password" data-i18n="[html]loginscreen.login">Login</a>
					</form>
				</div>	
	
				<p class="m-t">
					<small>
						<?= \App\Classes\Framework::getFrameWorkName() . ' ' . \App\Classes\Framework::getFrameWorkVersion();?>
						<span class="c-divider"> | </span>
						<?= date("D d-m-Y")?>
						<span class="c-divider"> | </span>
						<?= date("H:i:s");?>
						<span class="c-divider"> | </span>
						<?= \Config::APP_ENV . " " . \Config::APP_VER; ?><br>
						<a href="/privacy" data-i18n="[html]loginscreen.privacy">Privacy statement</a>
						<span class="c-divider"> | </span>
						<a href="/terms" data-i18n="[html]loginscreen.terms">Terms and conditions</a>
					</small>
				</p>
				
			</div>
		</div>
        <?php
        foreach($arr_js as $js) {
            echo '<script src="' . $js . '"></script>';
        }
        ?>
	</body>