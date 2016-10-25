<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
	<!-- Basic Page Needs
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php cf_leadya_title(); ?></title>
	
	<!-- Mobile Specific Metas
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<meta name="viewport" content="width=device-width, initial-scale=1"><?php
	
	cf_leadya_head();
	
?></head>
<body <?php body_class('casino-forms-page') ?>>

	<!-- Primary Page Layout
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<header id="header">
		<div class="container">
			<div class="row">
				<div class="branding four columns">
					<?php cf_leadya_logo(); ?>
				</div>
				<div class="navigation eight columns">
					<div class="toggle">
						<span class="t1"></span>
						<span class="t2"></span>
						<span class="t3"></span>
					</div>
					
					<?php do_action('icl_language_selector'); ?>
					
					<?php wp_nav_menu(array(
						'theme_location' => 'play-primary-menu',
						'walker' => new Walker_Playpage_Menu(),
						'menu_class' => 'menu',
						'container' => '',
						'depth' => 1
					)); ?>
				</div>
			</div>
		</div>
	</header>
	
	<div role="main" id="primary" class="container">