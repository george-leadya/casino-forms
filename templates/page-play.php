<?php



?><div class="row">
	<div class="content-heading columns twelve">
		<h1 class="page-title"><?php _e('Register', 'leadya'); ?></h1>
		<p><?php _e('Start Playing in just a few seconds', 'leadya'); ?></p>
	</div>
</div>

<div class="row">
	<div class="columns twelve">
		<ul class="tabs">
			<li class="current-menu-item"><span><?php _e('Step 1 of 2', 'leadya'); ?></span></li>
			<li class="next"><span><?php _e('Step 2 of 2', 'leadya'); ?></span></li>
		</ul>
		<div class="panels">
			<div id="step-1" class="row panel">
				<div class="columns six play-form">
					<div class="bonus-wrapper"><?php
						$r1 = mt_rand(1,20);
						
						if( file_exists( LEADYA_PLUGINDIR . 'images/ads/ads-' . $r1 . '.png' ) ){
							?><div class="bonus-container">
								<span class="bonus-tag"><?php _e('Exclusive', 'leadya'); ?></span>
								<img class="bonus-ad" src="<?php echo LEADYA_PLUGINURI . '/images/ads/ads-'. $r1 .'.png'; ?>" />
								<div class="bonus-content"><?php
									cf_leadya_top_bonus();
								?></div>
							</div><?php
						}
					?></div><?php
					
					/*<p class="aligncenter"> - <?php printf( '%s <a href="%s">%s</a>', __('or log in', 'leadya'), '#', __('here', 'leadya') ); ?> - </p>*/
					cf_leadya_get_template_part('play', 'form');
				?></div>
				
				<div class="columns six play-bonus">
					<div class="bonus-wrapper"><?php
						
						$r2 = mt_rand(1,20);
						
						// Make sure they're not the same ads
						while( $r2 == $r1 ){
							$r2 = mt_rand(0,20);
						}
						
						if( file_exists( LEADYA_PLUGINDIR . 'images/ads/ads-' . $r1 . '.png' ) ){
							?><div class="bonus-container">
								<span class="bonus-tag"><?php _e('Exclusive', 'leadya'); ?></span>
								<img class="bonus-ad" src="<?php echo LEADYA_PLUGINURI . '/images/ads/ads-'. $r1 .'.png'; ?>" />
								<div class="bonus-content"><?php
									cf_leadya_top_bonus();
								?></div>
							</div><?php
						}
						
						?><div class="bonus-sep">&nbsp;</div><?php
						
						if( file_exists( LEADYA_PLUGINDIR . 'images/ads/ads-' . $r2 . '.png' ) ){
							?><div class="bonus-container">
								<span class="bonus-tag"><?php _e('Exclusive', 'leadya'); ?></span>
								<img class="bonus-ad" src="<?php echo LEADYA_PLUGINURI . '/images/ads/ads-'. $r2 .'.png'; ?>" />
								<div class="bonus-content"><?php
									cf_leadya_bottom_bonus();
								?></div>
							</div><?php
						}
						
					?></div>
					
					<div id="certificates" class="row">
						<div class="columns six">
							<span class="cert"><img src="<?php echo LEADYA_PLUGINURI; ?>/images/ssl-safe-secure.jpg" alt="" /> <?php _e('Safe and Secure', 'leadya') ?></span>
						</div>
						<div class="columns six">
							<span class="cert"><img src="<?php echo LEADYA_PLUGINURI; ?>/images/quick-easy.jpg" alt="" /> <?php _e('Quick and easy', 'leadya') ?></span>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.panels -->
	</div>
</div>