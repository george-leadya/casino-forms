<form name="form_collector" id="form_collector" role="form" action="<?php echo cf_leadya_casino_form_action(); ?>" method="post">
	<fieldset>
		<label for="form_user_name"><?php _e('Name', 'leadya'); ?></label>
		<input type="text" name="form_user_name" id="form_user_name" value="" placeholder="<?php _e('Enter your name', 'leadya'); ?>" />
	</fieldset>
	
	<fieldset>
		<label for="form_user_email"><?php _e('Email', 'leadya'); ?> <small><?php _e('(Must be valid)', 'leadya'); ?></small></label>
		<input type="email" name="form_user_email" id="form_user_email" value="" placeholder="<?php _e('Enter your email', 'leadya'); ?>" />
	</fieldset>
	
	<fieldset>
		<label for="form_country"><?php _e('Select Country', 'leadya'); ?></label>
		<select name="form_country" id="form_country">
			<option value=""><?php _e('Select Country', 'leadya'); ?></option><?php
			
			$country = cf_leadya_getCountryCode();
			foreach( cf_leadya_getCountries() as $thiCountry=>$code ){
				printf(
					'<option value="%1$s" %2$s>%1$s</option>',
					$thiCountry,
					selected($country, $thiCountry, false)
				);
			}
			
		?></select>
	</fieldset>
	
	<fieldset>
		<label for="form_user_phone"><?php _e('Phone Number', 'leadya'); ?></label>
		<input type="text" name="form_user_area_code" id="form_user_area_code" placeholder="<?php _e('Area Code', 'leadya'); ?>" value="<?php echo cf_leadya_getAreaCode(); ?>" />
		<input type="tel" name="form_user_phone" id="form_user_phone" value="" placeholder="<?php _e('Enter your phone number', 'leadya'); ?>" />
	</fieldset><?php
	
	cf_leadya_hidden_fields();
	
	?><input type="submit" value="<?php _e('Continue to Step 2', 'leadya'); ?>" />
</form>