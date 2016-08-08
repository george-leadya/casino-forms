<?php

/**
 * Get template part
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function leadya_get_template_part( $slug, $name = '' ) {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/casino-forms/slug-name.php
    $template = locate_template( array( "casino-forms/{$slug}-{$name}.php", "{$slug}-{$name}.php" ) );

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( LEADYA_PLUGINDIR . "/templates/{$slug}-{$name}.php" ) ) {
        $template = LEADYA_PLUGINDIR . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/casino-forms/slug.php
    if ( ! $template ) {
        $template = locate_template( array( "{$slug}.php", "casino-forms/{$slug}.php" ) );
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters( 'leadya_get_template_part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Page Title
 */
function leady_title(){
	global $post;
	
	echo the_title_attribute(array('post'=>$post));
}

/**
 * Page <head> meta, styles and scripts
 */
function leadya_head(){
	do_action('leadya_head');
}

/**
 * Page footer scripts before the </body> tag
 */
function leadya_footer(){
	do_action('leadya_footer');
}

function cf_leadya_logo(){
	global $post;
	
	$logo = get_post_meta($post->ID, '_ladya_logo_id', true);
	// Get the image src
	$logo_src = wp_get_attachment_image_src( $logo, 'full' );
	
	// For convenience, see if the array is valid
	$have_logo_src = is_array( $logo_src );
	
	if( $have_logo_src ){
		printf( '<img src="%s" alt="%s" />', $logo_src[0], "");
	} else {
		printf( '<span class="site-title">%s</span>', get_bloginfo('name') );
	}
}

function leadya_top_bonus(){
	global $post;
	
	$bonus = get_post_meta($post->ID, '_leadya_bonus_a', true);
	echo apply_filters('the_content', $bonus);
}

function leadya_bottom_bonus(){
	global $post;
	
	$bonus = get_post_meta($post->ID, '_leadya_bonus_b', true);
	echo apply_filters('the_content', $bonus);
}

function leadya_hidden_fields(){
	global $post;
	$form_id = get_post_meta($post->ID, '_leadya_form_id', true);
	
	if( !empty($form_id) ){
		printf('<input type="hidden" id="form_collector_id" name="form_collector_id" value="%s" />', $form_id);
	}
	
	printf('<input type="hidden" id="form_submit" name="form_submit" value="Submit" />');
	printf('<input type="hidden" id="lang" name="lang" value="en" />');
	
	if( current_user_can( 'edit_pages' ) ){
		printf('<p><a href="%s">%s</a></p>', get_edit_post_link( $post->ID ), __('Edit Play Page', 'leadya'));
	}
}

function cf_leadya_footer_creds(){
	global $post;
	
	$creds = get_post_meta($post->ID, '_footer_creds', true);
	
	printf('<div class="credits">%s</div>', apply_filters('the_content', $creds));
}

function leadya_casino_form_action(){
	global $post;
	
	$protocol = "http://";
	$endpoint = "adduser2.php";
	
	if( is_ssl() ){ //action to take for page using SSL
		$protocol = "https://";
		$endpoint = "addusers2.php";
	}
	
	$endpoint = apply_filters( 'leadya_casino_form_action', $endpoint, $post );
	return $protocol . "www.allding.com/" . $endpoint;
}

function leadya_getCountryCode(){
	require_once( LEADYA_PLUGINDIR . '/includes/geoip/geoip.inc.php');
	require_once( LEADYA_PLUGINDIR . '/includes/geoip/geoipregionvars.php');

	$gi = geoip_open( LEADYA_PLUGINDIR . '/includes/geoip/GeoLiteCity.dat', GEOIP_MEMORY_CACHE);
	$country = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
	geoip_close($gi);
	
	return $country->country_name;
}

function leadya_getCountries(){
	$codes = array("Afghanistan" => "93", "Aland Islands" => "358", "Albania" => "355", "Algeria" => "213", "American Samoa" => "1", "Andorra" => "376", "Angola" => "244", "Anguilla" => "1", "Antarctica" => "672", "Antigua and Barbuda" => "1", "Argentina" => "54", "Armenia" => "374", "Aruba" => "297", "Ascension Island" => "247", "Australia" => "61", "Austria" => "43", "Azerbaijan" => "994", "Bahamas" => "1", "Bahrain" => "973", "Bangladesh" => "880", "Barbados" => "1", "Belarus" => "375", "Belgium" => "32", "Belize" => "501", "Benin" => "229", "Bermuda" => "1", "Bhutan" => "975", "Bolivia" => "591", "Bosnia and Herzegovina" => "387", "Botswana" => "267", "Brazil" => "55", "British Indian Ocean Territory" => "246", "Brunei Darussalam" => "673", "Burkina Faso" => "226", "Burundi" => "257", "Cambodia" => "855", "Cameroon" => "237", "Canada" => "1", "Canary Islands" => "34", "Cape Verde" => "238", "Cayman Islands" => "1", "Central African Republic" => "236", "Ceuta and Melilla" => "34", "Chad" => "235", "Chile" => "56", "China" => "86", "Christmas Island" => "61", "Cocos (Keeling) Island" => "61", "Colombia" => "57", "Comoros" => "269", "Congo" => "242", "Congo- Democratic Republic of the" => "243", "Cook Islands" => "682", "Costa Rica" => "506", "Côte D'Ivoire" => "225", "Croatia" => "385", "Cuba" => "53", "Cyprus- Republic of" => "357", "Cyprus- Turkish Republic of Northern" => "90", "Czech Republic" => "420", "Denmark" => "45", "Djibouti" => "253", "Dominica" => "1", "Dominican Republic" => "1", "Ecuador" => "593", "Egypt" => "20", "El Salvador" => "503", "Equatorial Guinea" => "240", "Eritrea" => "291", "Estonia" => "372", "Ethiopia" => "251", "Falkland Islands" => "500", "Faroe Islands" => "298", "Fiji" => "679", "Finland" => "358", "France" => "33", "French Guiana" => "594", "French Polynesia" => "689", "French Southern and Antarctic Lands" => "262", "Gabon" => "241", "Gambia" => "220", "Georgia" => "995", "Germany" => "49", "Ghana" => "233", "Gibraltar" => "350", "Greece" => "30", "Greenland" => "299", "Grenada" => "1", "Guadeloupe" => "590", "Guam" => "1", "Guatemala" => "502", "Guernsey" => "44", "Guinea" => "224", "Guinea-Bissau" => "245", "Guyana" => "592", "Haiti" => "509", "Holy See (Vatican City State)" => "39", "Honduras" => "504", "Hong Kong" => "852", "Hungary" => "36", "Iceland" => "354", "India" => "91", "Indonesia" => "62", "Iran" => "98", "Iraq" => "964", "Ireland" => "353", "Isle of Man" => "44", "Israel" => "972", "Italy" => "39", "Jamaica" => "1", "Japan" => "81", "Jersey" => "44", "Jordan" => "962", "Kazakhstan" => "7", "Kenya" => "254", "Kiribati" => "686", "Korea- Democratic People's Republic of" => "850", "Korea- Republic of" => "82", "Kuwait" => "965", "Kyrgyz Republic" => "996", "Laos" => "856", "Latvia" => "371", "Lebanon" => "961", "Lesotho" => "266", "Liberia" => "231", "Libya" => "218", "Liechtenstein" => "423", "Lithuania" => "370", "Luxembourg" => "352", "Macao" => "853", "Macedonia" => "389", "Madagascar" => "261", "Malawi" => "265", "Malaysia" => "60", "Maldives" => "960", "Mali" => "223", "Malta" => "356", "Marshall Islands" => "692", "Martinique" => "596", "Mauritania" => "222", "Mauritius" => "230", "Mayotte" => "269", "Mexico" => "52", "Micronesia" => "691", "Moldova" => "373", "Monaco" => "377", "Mongolia" => "976", "Montenegro" => "382", "Montserrat" => "1", "Morocco" => "212", "Mozambique" => "258", "Myanmar" => "95", "Namibia" => "264", "Nauru" => "674", "Nepal" => "977", "Netherlands" => "31", "Netherlands Antilles" => "599", "New Caledonia" => "687", "New Zealand" => "64", "Nicaragua" => "505", "Niger" => "227", "Nigeria" => "234", "Niue" => "683", "Norfolk Island" => "672", "Northern Mariana Islands" => "1", "Norway" => "47", "Oman" => "968", "Pakistan" => "92", "Palau" => "680", "Palestine" => "970", "Panama" => "507", "Papua New Guinea" => "675", "Paraguay" => "595", "Peru" => "51", "Philippines" => "63", "Pitcairn" => "872", "Poland" => "48", "Portugal" => "351", "Puerto Rico" => "1", "Qatar" => "974", "Réunion" => "262", "Romania" => "40", "Russian Federation" => "7", "Rwanda" => "250", "Saint Helena" => "290", "Saint Kitts and Nevis" => "1", "Saint Lucia" => "1", "Saint Pierre and Miquelon" => "508", "Saint Vincent and the Grenadines" => "1", "Samoa" => "685", "San Marino" => "378", "São Tome and Principe" => "239", "Saudi Arabia" => "966", "Senegal" => "221", "Serbia" => "381", "Seychelles" => "248", "Sierra Leone" => "232", "Singapore" => "65", "Slovakia" => "421", "Slovenia" => "386", "Solomon Islands" => "677", "Somalia" => "252", "Somaliland" => "252", "South Africa" => "27", "Spain" => "34", "Sri Lanka" => "94", "Sudan" => "249", "Suriname" => "597", "Svalbard and Jan Mayen" => "47", "Swaziland" => "268", "Sweden" => "46", "Switzerland" => "41", "Syria" => "963", "Taiwan" => "886", "Tajikistan" => "992", "Tanzania" => "255", "Thailand" => "66", "Timor-Leste" => "670", "Togo" => "228", "Tokelau" => "690", "Tonga" => "676", "Trinidad and Tobago" => "1", "Tristan da Cunha" => "290", "Tunisia" => "216", "Turkey" => "90", "Turkmenistan" => "993", "Turks and Caicos Islands" => "1", "Tuvalu" => "688", "Uganda" => "256", "Ukraine" => "380", "United Arab Emirates" => "971", "United Kingdom" => "44", "United States" => "1", "United States Minor Outlying Islands" => "699", "Uruguay" => "598", "Uzbekistan" => "998", "Vanuatu" => "678", "Venezuela" => "58", "Viet Nam" => "84", "Virgin Islands- British" => "1", "Virgin Islands- U.S." => "1", "Wallis and Futuna Islands" => "681", "Western Sahara" => "212", "Yemen" => "967", "Zambia" => "260", "Zimbabwe" => "263");
	
	return $codes;
}

function leadya_getAreaCode(){
	$codes = leadya_getCountries();
	$countryCode = leadya_getCountryCode();
	return "+".$codes[$countryCode];
}