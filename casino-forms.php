<?php
/*
 * Plugin Name: Casino Forms
 * Plugin URI:  http://leadya.com.ph/
 * Description: Custom Template engine for Casino and Game site's play pages
 * Version:     0.1.2
 * Author:      George L.
 * Author URI:  http://iamgeorgeleis.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: my-toolset
 
Casino Forms is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Casino Forms is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Casino Forms. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define('LEADYA_PLUGINDIR', plugin_dir_path( __FILE__ ));
define('LEADYA_PLUGINURI', plugin_dir_url( __FILE__ ));

register_activation_hook( __FILE__, 'cf_leadya_activate_hook' );
register_deactivation_hook( __FILE__, 'cf_leadya_deactivate_hook' );

function cf_leadya_activate_hook(){
}

function cf_leadya_deactivate_hook(){
	flush_rewrite_rules();
}

include_once( LEADYA_PLUGINDIR . 'includes/plugin-updater.php');
include_once( LEADYA_PLUGINDIR . 'includes/misc-functions.php');
include_once( LEADYA_PLUGINDIR . 'includes/walker-nav-menu.php');
include_once( LEADYA_PLUGINDIR . 'includes/pagetemplater.php');

if ( is_admin() ) {
    new CF_Leadya_GitHubPluginUpdater( __FILE__, 'george-leadya', "casino-forms" );
}

class Casino_Forms{
	
	private $version;
	
	public $clean = false;
	
	/**
     * Plugin initialization.
     */
	public function __construct(){
		$this->version = '0.1';
		
		add_action( 'plugins_loaded',	array( 'PageTemplater', 'get_instance' ) );
		add_action( 'plugins_loaded',	array( $this, 'load_plugin_textdomain' ) );
		add_action( 'plugins_loaded',	array( $this, 'register_my_menu' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_action( 'init',				array( $this, 'remove_editor_init' ) );
		add_action( 'init',				array( $this, 'yoast_filters' ) );
		add_action( 'add_meta_boxes',	array( $this, 'wpdocs_register_meta_boxes' ) );
		add_action( 'save_post',		array( $this, 'save_metabox' ), 10, 2 );
		
		add_action( 'cf_leadya_head',	array( $this, 'leadya_head') );
		add_action( 'cf_leadya_head',	array( $this, 'leadya_theme_style') );
		add_action( 'cf_leadya_head',	array( $this, 'leadya_google_analytics') );
		
		add_action( 'cf_leadya_footer',	array( $this, 'leadya_footer') );
		
		add_filter( 'leadya_play_template', array( $this, 'leadya_play_redirect' ), 10, 2 );
		
		// Plugin settings page
		add_action( 'admin_menu', array( $this, 'wpdocs_register_settings_page') );
		add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
	}
	
	public function register_my_menu(){
		register_nav_menu( 'play-primary-menu', __( 'Play Page Menu', 'leadya' ) );
	}
	
	public function load_plugin_textdomain(){
		load_plugin_textdomain( 'leadya', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	public function remove_editor_init(){
		// If not in the admin, return.
		if ( ! is_admin() ) {
		return;
		}
		
		// Get the post ID on edit post with filter_input super global inspection.
		$current_post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		// Get the post ID on update post with filter_input super global inspection.
		$update_post_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT );
		
		// Check to see if the post ID is set, else return.
		if ( isset( $current_post_id ) ) {
			$post_id = absint( $current_post_id );
		} else if ( isset( $update_post_id ) ) {
			$post_id = absint( $update_post_id );
		} else {
			return;
		}
		
		// Don't do anything unless there is a post_id.
		if ( isset( $post_id ) ) {
			// Get the template of the current post.
			$template = get_post_meta( $post_id, '_wp_page_template', true );
			
			// Example of removing page editor for page-your-template.php template.
			if (  'template-leadya-play.php' === $template ) {
				remove_post_type_support( 'page', 'editor' );
				remove_post_type_support( 'page', 'author' );
				remove_post_type_support( 'page', 'excerpt' );
				remove_post_type_support( 'page', 'comments' );
				remove_post_type_support( 'page', 'thumbnail' );
				remove_post_type_support( 'page', 'post-formats' );
				remove_post_type_support( 'page', 'custom-fields' );
			}
		}
	}
	
	public function yoast_filters(){
		if( defined('WPSEO_VERSION') ){
			$frontend = WPSEO_Frontend::get_instance();
			
			add_action( 'wp_head', array( $frontend, 'front_page_specific_init' ), 0 );
			add_action( 'cf_leadya_head', array( $frontend, 'head' ), 1 );
			add_filter( 'cf_leadya_title', array( $frontend, 'title' ), 15, 3 );
		}
	}
	
	/**
     * Adds the meta box.
     */
	public function wpdocs_register_meta_boxes(){
		$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
		$template = get_post_meta($post_id,'_wp_page_template',TRUE);
		
		if( !$post_id ) return;
		if($template !== 'template-leadya-play.php') return;
		
		add_meta_box( 'playpage', __( 'Play Page Configuration', 'leadya' ), array( $this, 'wpdocs_display_metabox' ), 'page' );
	}
	
	/**
     * Renders the meta box.
     */
	public function wpdocs_display_metabox( $post, $metabox ){
		$template = get_post_meta($post->ID, '_wp_page_template', true);
		
		if( !$post ) return;
		if($template !== 'template-leadya-play.php') return;
		
		// Add nonce for security and authentication.
        wp_nonce_field( 'leadya_nonce_action', 'leadya_nonce' );
		
		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
		
		// See if there's a media id already saved as post meta
		$logo_id = get_post_meta( $post->ID, '_ladya_logo_id', true );
		// Get the image src
		$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
		// For convenience, see if the array is valid
		$have_logo_src = is_array( $logo_src );
		
		$form_id = get_post_meta( $post->ID, '_leadya_form_id', true);
		$ga_id = get_post_meta( $post->ID, '_leadya_ga_id', true);
		$bonus1 = get_post_meta( $post->ID, '_leadya_bonus_a', true);
		$bonus2 = get_post_meta( $post->ID, '_leadya_bonus_b', true);
		$footer_creds = get_post_meta( $post->ID, '_footer_creds', true);
		$with_theme = get_post_meta( $post->ID, '_leadya_with_theme', true);
		$head_script = get_post_meta( $post->ID, '_leadya_head_script', true);
		$foot_script = get_post_meta( $post->ID, '_leadya_foot_script', true);
		
		?><div id="playpagestuff">
			<div id="ajax-response"></div>
			
			<p><strong><?php _e('Casino Play Page', 'leadya'); ?></strong></p>
			
			<table id="playpagetable" class="form-table">
				<thead></thead>
				<tbody>
					<tr>
						<th scope="row"><label for="form_id"><?php _e('Include Theme Style?', 'leadya'); ?></label></th>
						<td>
							<label><input type="checkbox" class="regular-checkbox" name="with_theme" id="with_theme" value="1" <?php checked(1, $with_theme); ?> /> <?php _e('Add Styles from your current theme.', 'leadya'); ?></label>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="logo"><?php _e('Logo', 'leadya'); ?></label></th>
						<td>
							<div class="custom-img-container field-logo"><?php
								if ( $have_logo_src ) :
									?><a class="button-upload upload-logo-img" href="<?php echo $upload_link ?>" data-field="logo" data-title="Logo"><img src="<?php echo $logo_src[0] ?>" alt="" style="max-width:100%;" /></a><?php
								endif;
							?></div>
							
							<p class="hide-if-no-js">
								<a class="button button-upload upload-logo-img <?php if ( $have_logo_src  ) { echo 'hidden'; } ?>" href="<?php echo $upload_link ?>" data-field="logo" data-title="Logo"><?php _e('Set Logo', 'leadya') ?></a>
								<a class="button button-delete delete-logo-img <?php if ( !$have_logo_src  ) { echo 'hidden'; } ?>" href="#" data-field="logo"><?php _e('Remove Logo', 'leadya') ?></a>
							</p>
							
							<input class="logo-img-id" name="logo" id="logo" type="hidden" value="<?php echo esc_attr( $logo_id ); ?>" />
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="form_id"><?php _e('Form ID', 'leadya'); ?></label></th>
						<td>
							<input type="text" class="regular-text" name="form_id" id="form_id" value="<?php echo $form_id ?>" placeholder="Enter a form ID" />
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="ga_id"><?php _e('Google Analytics ID', 'leadya'); ?></label></th>
						<td>
							<input type="text" class="regular-text" name="ga_id" id="ga_id" value="<?php echo $ga_id ?>" placeholder="Enter Google Analytics ID" />
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<h3><label for="bunus_a"><?php _e('First Bonus Widget Contents', 'leadya'); ?></label></h3><?php
							wp_editor( $bonus1, 'bunus_a', array(
								'wpautop' => false,
								'media_buttons' => false,
								'textarea_name' => 'bonus_a',
								'tinymce' => array( 
									'content_css' => trailingslashit(LEADYA_PLUGINURI) . 'css/editor-styles.css' 
								)
							) );
						?></td>
					</tr>
					
					<tr>
						<td colspan="2">
							<h3><label for="bunus_b"><?php _e('Second Bonus Widget Contents', 'leadya'); ?></label></h3><?php
							wp_editor( $bonus2, 'bunus_b', array(
								'wpautop' => false,
								'media_buttons' => false,
								'textarea_name' => 'bonus_b',
								'tinymce' => array( 
									'content_css' => trailingslashit(LEADYA_PLUGINURI) . 'css/editor-styles.css' 
								)
							) );
						?></td>
					</tr>
					
					<tr>
						<td colspan="2">
							<h3><label for="bunus_b"><?php _e('Footer Credits', 'leadya'); ?></label></h3><?php
							wp_editor( $footer_creds, 'footer_creds', array(
								'wpautop' => false,
								'media_buttons' => false,
								'textarea_name' => 'footer_creds',
								'tinymce' => array( 
									'content_css' => trailingslashit(LEADYA_PLUGINURI) . 'css/editor-styles.css' 
								)
							) );
						?></td>
					</tr>
					
					<tr>
						<th scope="row"><label for="head_script"><?php _e('Custom Header Script', 'leadya'); ?></label></th>
						<td>
							<textarea class="regular-textarea" name="head_script" id="head_script" placeholder="Enter your custom scripts"><?php echo esc_attr($head_script); ?></textarea>
							<p class="description"><?php _e('Appended inside the &lt;head&gt; tag', 'leadya'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="foot_script"><?php _e('Custom Footer Script', 'leadya'); ?></label></th>
						<td>
							<textarea class="regular-textarea" name="foot_script" id="foot_script" placeholder="Enter your custom scripts"><?php echo esc_attr($foot_script); ?></textarea>
							<p class="description"><?php _e('Appended before the &lt;/body&gt; tag', 'leadya'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div><?php
	}
	
	/**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
		// Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['leadya_nonce'] ) ? $_POST['leadya_nonce'] : '';
        $nonce_action = 'leadya_nonce_action';
		
		// Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
		
		// Save Play Page Configuration
		if( !empty($_POST['form_id']) ){
			update_post_meta( $post->ID, '_leadya_form_id', $_POST['form_id'] );
		} else {
			delete_post_meta( $post->ID, '_leadya_form_id');
		}
		
		if( !empty($_POST['ga_id']) ){
			update_post_meta( $post->ID, '_leadya_ga_id', $_POST['ga_id'] );
		} else {
			delete_post_meta( $post->ID, '_leadya_ga_id');
		}
		
		if( !empty($_POST['logo']) ){
			update_post_meta( $post->ID, '_ladya_logo_id', $_POST['logo'] );
		} else {
			delete_post_meta( $post->ID, '_ladya_logo_id');
		}
		
		if( !empty($_POST['bonus_a']) ){
			update_post_meta( $post->ID, '_leadya_bonus_a', $_POST['bonus_a'] );
		} else {
			delete_post_meta( $post->ID, '_leadya_bonus_a');
		}
		
		if( !empty($_POST['bonus_b']) ){
			update_post_meta( $post->ID, '_leadya_bonus_b', $_POST['bonus_b'] );
		} else {
			delete_post_meta( $post->ID, '_leadya_bonus_b');
		}
		
		if( !empty($_POST['footer_creds']) ){
			update_post_meta( $post->ID, '_footer_creds', $_POST['footer_creds'] );
		} else {
			delete_post_meta( $post->ID, '_footer_creds');
		}
		
		if( !empty($_POST['with_theme']) ){
			update_post_meta( $post->ID, '_leadya_with_theme', 1 );
		} else {
			delete_post_meta( $post->ID, '_leadya_with_theme');
		}
		
		if( !empty($_POST['head_script']) ){
			update_post_meta( $post->ID, '_leadya_head_script', $_POST['head_script'] );
		} else {
			delete_post_meta( $post->ID, '_leadya_head_script');
		}
		
		if( !empty($_POST['foot_script']) ){
			update_post_meta( $post->ID, '_leadya_foot_script', $_POST['foot_script'] );
		} else {
			delete_post_meta( $post->ID, '_leadya_foot_script');
		}
	}
	
	/**
     * Registers the JavaScript for handling the media uploader.
     *
     * @since 0.1.0
     */
    public function enqueue_scripts() {
		wp_enqueue_media();
		
		wp_enqueue_script(
            'leadya_media_uploader',
            trailingslashit(LEADYA_PLUGINURI) . 'js/media-uploader.js',
            array( 'jquery' ),
            $this->version
        );
		
		wp_enqueue_style(
            'leadya_admin_style',
            trailingslashit(LEADYA_PLUGINURI) . 'css/admin.css'
        );
    }
	
	public function leadya_head(){
		global $post;
		
		wp_register_style( 'cf_leadya_googlefonts', 'https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic|Lato:400,300,700,900' );
		wp_register_style( 'cf_leadya_normalize', trailingslashit(LEADYA_PLUGINURI) . 'css/normalize.css' );
		wp_register_style( 'cf_leadya_skeleton', trailingslashit(LEADYA_PLUGINURI) . 'css/skeleton.css' );
		wp_register_style( 'cf_leadya_custom', trailingslashit(LEADYA_PLUGINURI) . 'css/custom.css' );
		wp_register_style( 'theme_style', get_bloginfo('stylesheet_url') );
		
		wp_register_script( 'modrnizr', trailingslashit(LEADYA_PLUGINURI) . 'js/modernizr.min.js' );
		
		if( $this->clean === false ){
			wp_print_styles( 'cf_leadya_googlefonts' );
			wp_print_styles( 'cf_leadya_normalize' );
			wp_print_styles( 'cf_leadya_skeleton' );
			wp_print_styles( 'cf_leadya_custom' );
			
			wp_print_scripts( 'cf_leadya_modrnizr' );
		}
		
		$scripts = get_post_meta($post->ID, '_leadya_head_script', true);
		if( !empty($scripts) ){ echo $scripts . "\n"; }
		
		$custom_styles = get_option('global_css', false);
		if( $custom_styles ){
			echo  "\n" . '<style type="text/css">' . "\n";
			echo $custom_styles;
			echo "\n" . '</style>' . "\n";
		}
	}
	
	public function leadya_footer(){
		global $post;
		
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', trailingslashit(LEADYA_PLUGINURI) . 'js/jquery-3.0.0.min.js' );
		wp_register_script( 'cf_leadya_validate', trailingslashit(LEADYA_PLUGINURI) . 'js/jquery.validate.js' );
		wp_register_script( 'cf_leadya_main', trailingslashit(LEADYA_PLUGINURI) . 'js/main.js' );
		
		if( $this->clean === false ){
			wp_print_scripts( 'jquery' );
			wp_print_scripts( 'cf_leadya_validate' );
			wp_print_scripts( 'cf_leadya_main' );
		}
		
		$scripts = get_post_meta($post->ID, '_leadya_foot_script', true);
		if( !empty($scripts) ){ echo $scripts . "\n"; }
	}
	
	public function leadya_theme_style(){
		global $post;
		
		$enabled = get_post_meta($post->ID, '_leadya_with_theme', true);
		if( $enabled ){
			wp_print_styles('theme_style');
		}
	}
	
	public function leadya_google_analytics(){
		global $post;
		
		$ga_id = get_post_meta($post->ID, '_leadya_ga_id', true);
		
		if( !empty($ga_id) ){
			?><script type="text/javascript">
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '<?php echo $ga_id; ?>']);
				_gaq.push(['_gat._forceSSL']);
				_gaq.push(['_trackPageview']);
				(function () {
					var ga = document.createElement('script');
					ga.type = 'text/javascript';
					ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(ga, s);
				})();
			</script><?php
		}
	}
	
	public function leadya_play_redirect( $post, $file ){
		/*if(!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true)])){
			return $file;
		}*/
		
		$lastSegment = basename(parse_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", PHP_URL_PATH));
		
		if( is_numeric($lastSegment) ){
			$this->clean = true;
			$file = LEADYA_PLUGINDIR . 'templates/play-redirect.php';
		}
		
		return $file;
	}
	
	public function wpdocs_register_settings_page(){
		add_submenu_page( 'options-general.php', __('Casino Forms', 'leadya'), __('Casino Forms', 'leadya'), 'manage_options', 'casino-forms-settings-page', array( $this, 'wpdocs_settings_page_callback' ) );
	}
	
	public function wpdocs_settings_page_callback(){
		?><div class="wrap casino-settings"><div id="icon-tools" class="icon32"></div>
			<h2><?php _e('Casino Forms Settings', 'leadya'); ?></h2>
			
			<form method="post" action="options.php"><?php
			
				settings_fields( 'casino-forms-settings-group' );
				do_settings_sections( 'casino-forms-settings-group' );
				
				?><table class="form-table">
					<tr valign="top">
						<th colspan="2">
							<h4><label for="global_css"><?php _e('Global CSS', 'leadya'); ?></label></h4>
							<textarea type="text" name="global_css" id="global_css" placeholder="CSS code"><?php echo esc_attr( get_option('global_css') ); ?></textarea>
						</th>
					</tr>
				</table>
				
				<?php submit_button(); ?>
			</form>
		</div><?php
	}
	
	public function register_plugin_settings(){
		register_setting( 'casino-forms-settings-group', 'global_css' );
	}
}

global $cf_lead_forms;
$cf_lead_forms = new Casino_Forms();