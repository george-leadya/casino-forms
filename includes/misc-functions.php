<?php
/**
 * Get template part
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
require_once( LEADYA_PLUGINDIR . '/includes/geoip/geoip.inc.php');
require_once( LEADYA_PLUGINDIR . '/includes/geoip/geoipregionvars.php');

function cf_leadya_get_template_part( $slug, $name = '' ) {
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
    $template = apply_filters( 'cf_leadya_get_template_part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Page Title
 */
function cf_leadya_title(){
	global $post;
	
	echo apply_filters( 'cf_leadya_title', get_the_title($post->ID) );
}

/**
 * Page <head> meta, styles and scripts
 */
function cf_leadya_head(){
	do_action('cf_leadya_head');
}

/**
 * Page footer scripts before the </body> tag
 */
function cf_leadya_footer(){
	do_action('cf_leadya_footer');
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

function cf_leadya_top_bonus(){
	global $post;
	
	$bonus = get_post_meta($post->ID, '_leadya_bonus_a', true);
	echo apply_filters('the_content', $bonus);
}

function cf_leadya_bottom_bonus(){
	global $post;
	
	$bonus = get_post_meta($post->ID, '_leadya_bonus_b', true);
	echo apply_filters('the_content', $bonus);
}

function cf_leadya_hidden_fields(){
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

function cf_leadya_casino_form_action(){
	global $post;
	
	$protocol = "http://";
	$endpoint = "adder.php";
	
	if( is_ssl() ){ //action to take for page using SSL
		$protocol = "https://";
	}
	
	$endpoint = apply_filters( 'cf_leadya_casino_form_action', $endpoint, $post );
	return $protocol . "www.allding.com/" . $endpoint;
}

function cf_leadya_getCountryCode(){
	$apiquery = "http://freegeoip.net/json/" . $_SERVER['REMOTE_ADDR'];
	$ch = curl_init($apiquery);
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = json_decode(curl_exec($ch));
	curl_close($ch);
	
	if( !$data->country_code ){
		$gi = geoip_open( LEADYA_PLUGINDIR . '/includes/geoip/GeoLiteCity.dat', GEOIP_MEMORY_CACHE);
		$country = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
		geoip_close($gi);
		
		$returned_data = $country->country_name;
	} else {
		$returned_data = $data->country_name;
	}
	
	return $returned_data;
}

function cf_leadya_getCountries(){
	$codes = array(
		"Afghanistan" => "93",
		"Albania" => "355",
		"Algeria" => "213",
		"American Samoa" => "684",
		"Andorra" => "376",
		"Angola" => "244",
		"Anguilla" => "264",
		"Antarctica" => "672",
		"Antigua and Barbuda" => "1",
		"Argentina" => "54",
		"Armenia" => "374",
		"Aruba" => "297",
		"Australia" => "61",
		"Austria" => "43",
		"Azerbaijan" => "994",
		"Bahamas" => "1",
		"Bahrain" => "973",
		"Bangladesh" => "880",
		"Barbados" => "1",
		"Belarus" => "375",
		"Belgium" => "32",
		"Belize" => "501",
		"Benin" => "229",
		"Bermuda" => "441",
		"Bhutan" => "975",
		"Bolivia" => "591",
		"Bosnia and Herzegovina" => "387",
		"Botswana" => "267",
		"Brazil" => "55",
		"British Indian Ocean Territory" => "246",
		"British Virgin Islands" => "284",
		"Brunei" => "673",
		"Bulgaria" => "359",
		"Burkina Faso" => "226",
		"Burundi" => "257",
		"Cambodia" => "855",
		"Cameroon" => "237",
		"Canada" => "1",
		"Cape Verde" => "238",
		"Cayman Islands" => "345",
		"Central African Republic" => "236",
		"Chad" => "235",
		"Chile" => "56",
		"China" => "86",
		"Christmas Island" => "61",
		"Cocos Islands" => "61",
		"Colombia" => "57",
		"Comoros" => "269",
		"Cook Islands" => "682",
		"Costa Rica" => "506",
		"Croatia" => "385",
		"Cuba" => "53",
		"Curacao" => "599",
		"Cyprus" => "357",
		"Czech Republic" => "420",
		"Democratic Republic of the Congo" => "243",
		"Denmark" => "45",
		"Djibouti" => "253",
		"Dominica" => "767",
		"Dominican Republic" => "809",
		"East Timor" => "670",
		"Ecuador" => "593",
		"Egypt" => "20",
		"El Salvador" => "503",
		"Equatorial Guinea" => "240",
		"Eritrea" => "291",
		"Estonia" => "372",
		"Ethiopia" => "251",
		"Falkland Islands" => "500",
		"Faroe Islands" => "298",
		"Fiji" => "679",
		"Finland" => "358",
		"France" => "33",
		"French Polynesia" => "689",
		"Gabon" => "241",
		"Gambia" => "220",
		"Georgia" => "995",
		"Germany" => "49",
		"Ghana" => "233",
		"Gibraltar" => "350",
		"Greece" => "30",
		"Greenland" => "299",
		"Grenada" => "473",
		"Guam" => "671",
		"Guatemala" => "502",
		"Guernsey" => "44-1481",
		"Guinea" => "224",
		"Guinea-Bissau" => "245",
		"Guyana" => "592",
		"Haiti" => "509",
		"Honduras" => "504",
		"Hong Kong" => "852",
		"Hungary" => "36",
		"Iceland" => "354",
		"India" => "91",
		"Indonesia" => "62",
		"Iran" => "98",
		"Iraq" => "964",
		"Ireland" => "353",
		"Isle of Man" => "44-1624",
		"Israel" => "972",
		"Italy" => "39",
		"Ivory Coast" => "225",
		"Jamaica" => "876",
		"Japan" => "81",
		"Jersey" => "44-1534",
		"Jordan" => "962",
		"Kazakhstan" => "7",
		"Kenya" => "254",
		"Kiribati" => "686",
		"Kosovo" => "383",
		"Kuwait" => "965",
		"Kyrgyzstan" => "996",
		"Laos" => "856",
		"Latvia" => "371",
		"Lebanon" => "961",
		"Lesotho" => "266",
		"Liberia" => "231",
		"Libya" => "218",
		"Liechtenstein" => "423",
		"Lithuania" => "370",
		"Luxembourg" => "352",
		"Macau" => "853",
		"Macedonia" => "389",
		"Madagascar" => "261",
		"Malawi" => "265",
		"Malaysia" => "60",
		"Maldives" => "960",
		"Mali" => "223",
		"Malta" => "356",
		"Marshall Islands" => "692",
		"Mauritania" => "222",
		"Mauritius" => "230",
		"Mayotte" => "262",
		"Mexico" => "52",
		"Micronesia" => "691",
		"Moldova" => "373",
		"Monaco" => "377",
		"Mongolia" => "976",
		"Montenegro" => "382",
		"Montserrat" => "664",
		"Morocco" => "212",
		"Mozambique" => "258",
		"Myanmar" => "95",
		"Namibia" => "264",
		"Nauru" => "674",
		"Nepal" => "977",
		"Netherlands" => "31",
		"Netherlands Antilles" => "599",
		"New Caledonia" => "687",
		"New Zealand" => "64",
		"Nicaragua" => "505",
		"Niger" => "227",
		"Nigeria" => "234",
		"Niue" => "683",
		"North Korea" => "850",
		"Northern Mariana Islands" => "670",
		"Norway" => "47",
		"Oman" => "968",
		"Pakistan" => "92",
		"Palau" => "680",
		"Palestine" => "970",
		"Panama" => "507",
		"Papua New Guinea" => "675",
		"Paraguay" => "595",
		"Peru" => "51",
		"Philippines" => "63",
		"Pitcairn" => "64",
		"Poland" => "48",
		"Portugal" => "351",
		"Puerto Rico" => "787",
		"Qatar" => "974",
		"Republic of the Congo" => "242",
		"Reunion" => "262",
		"Romania" => "40",
		"Russia" => "7",
		"Rwanda" => "250",
		"Saint Barthelemy" => "590",
		"Saint Helena" => "290",
		"Saint Kitts and Nevis" => "869",
		"Saint Lucia" => "758",
		"Saint Martin" => "590",
		"Saint Pierre and Miquelon" => "508",
		"Saint Vincent and the Grenadines" => "784",
		"Samoa" => "685",
		"San Marino" => "378",
		"Sao Tome and Principe" => "239",
		"Saudi Arabia" => "966",
		"Senegal" => "221",
		"Serbia" => "381",
		"Seychelles" => "248",
		"Sierra Leone" => "232",
		"Singapore" => "65",
		"Sint Maarten" => "721",
		"Slovakia" => "421",
		"Slovenia" => "386",
		"Solomon Islands" => "677",
		"Somalia" => "252",
		"South Africa" => "27",
		"South Korea" => "82",
		"South Sudan" => "211",
		"Spain" => "34",
		"Sri Lanka" => "94",
		"Sudan" => "249",
		"Suriname" => "597",
		"Svalbard and Jan Mayen" => "47",
		"Swaziland" => "268",
		"Sweden" => "46",
		"Switzerland" => "41",
		"Syria" => "963",
		"Taiwan" => "886",
		"Tajikistan" => "992",
		"Tanzania" => "255",
		"Thailand" => "66",
		"Togo" => "228",
		"Tokelau" => "690",
		"Tonga" => "676",
		"Trinidad and Tobago" => "868",
		"Tunisia" => "216",
		"Turkey" => "90",
		"Turkmenistan" => "993",
		"Turks and Caicos Islands" => "649",
		"Tuvalu" => "688",
		"U.S. Virgin Islands" => "340",
		"Uganda" => "256",
		"Ukraine" => "380",
		"United Arab Emirates" => "971",
		"United Kingdom" => "44",
		"United States" => "1",
		"Uruguay" => "598",
		"Uzbekistan" => "998",
		"Vanuatu" => "678",
		"Vatican" => "379",
		"Venezuela" => "58",
		"Vietnam" => "84",
		"Wallis and Futuna" => "681",
		"Western Sahara" => "212",
		"Yemen" => "967",
		"Zambia" => "260",
		"Zimbabwe" => "263"
	);
	
	return $codes;
}

function cf_leadya_getAreaCode(){
	$codes = cf_leadya_getCountries();
	$countryCode = cf_leadya_getCountryCode();
	return "+".$codes[$countryCode];
}