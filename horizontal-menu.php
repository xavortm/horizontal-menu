<?php
/**
 * Plugin Name: Horizontal Menus
 * Description: Use horizontal admin menu instead of vertical one.
 * Author: Alex Dimitrov
 * Author URI: http://xavortm.com
 * Version: 0.1
 * Text Domain: horizontal-menu
 * License: GPL2

 Copyright 2011 Alex Dimitrov (email : xavortm AT gmail DOT com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Get some constants ready for paths when your plugin grows
 *
 */

define( 'HM_VERSION', '0.1' );
define( 'HM_PATH', dirname( __FILE__ ) );
define( 'HM_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'HM_FOLDER', basename( HM_PATH ) );
define( 'HM_URL', plugins_url() . '/' . HM_FOLDER );
define( 'HM_URL_INCLUDES', HM_URL . '/inc' );


/**
 *
 * The plugin base class - the root of all WP goods!
 *
 * @author nofearinc
 *
 */
class Horizontal_Menu {

	/**
	 *
	 * Assign everything as a call from within the constructor
	 */
	public function __construct() {
		// add script and style calls the WP way
		// it's a bit confusing as styles are called with a scripts hook
		// @blamenacin - http://make.wordpress.org/core/2011/12/12/use-wp_enqueue_scripts-not-wp_print_styles-to-enqueue-scripts-and-styles-for-the-frontend/
		// add_action( 'wp_enqueue_scripts', array( $this, 'hm_add_JS' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'hm_add_CSS' ) );

		// register admin pages for the plugin
		add_action( 'admin_menu', array( $this, 'hm_admin_pages_callback' ) );

		// register meta boxes for Pages (could be replicated for posts and custom post types)
		// add_action( 'add_meta_boxes', array( $this, 'hm_meta_boxes_callback' ) );

		// register save_post hooks for saving the custom fields
		// add_action( 'save_post', array( $this, 'hm_save_sample_field' ) );

		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, 'hm_on_activate_callback' );
		register_deactivation_hook( __FILE__, 'hm_on_deactivate_callback' );

		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'hm_add_textdomain' ) );

		// Load the plugin files here only if the user want's this menu active
		add_action( 'plugins_loaded', array( $this, 'hm_load_menu' ) );

		// Add earlier execution as it needs to occur before admin page display
		add_action( 'admin_init', array( $this, 'hm_register_settings' ), 5 );

		// The script to take care of the plugin toggling
		// add_action( 'admin_footer', array( $this, 'hm_activate_deactivate_ajax' ) );
		// add_action( 'wp_ajax_toggle_plugin', array( $this, 'toggle_plugin_callback' ) );

		// Add a sample shortcode
		// add_action( 'init', array( $this, 'hm_sample_shortcode' ) );

		// Add a sample widget
		// add_action( 'widgets_init', array( $this, 'hm_sample_widget' ) );

		/*
		 * TODO:
		 * 		template_redirect
		 */

		// Add actions for storing value and fetching URL
		// use the wp_ajax_nopriv_ hook for non-logged users (handle guest actions)
		add_action( 'wp_ajax_store_ajax_value', array( $this, 'store_ajax_value' ) );
		add_action( 'wp_ajax_fetch_ajax_url_http', array( $this, 'fetch_ajax_url_http' ) );

	}

	public function hm_activate_deactivate_ajax() {
		?>
			<script type="text/javascript" >
			jQuery(document).ready(function($) {

				jQuery(".horizontal-menu-toggle").on( "click", function(e) {
					e.preventDefault;

					var $button = jQuery(this);
					var toggle = "deactivate";

					if ( $button.hasClass("activate-horizontal-menu") ) {
						toggle = "activate";
					}

					var data = {
						'action': 'toggle_plugin',
						'toggle': toggle
					};

					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					jQuery.post(ajaxurl, data, function(response) {
						console.log(response);
						// location.reload();
					});
				});
			});
			</script>
		<?php
	}

	public function toggle_plugin_callback() {
		$toggle_state = $_POST['toggle'];
		$current_user = wp_get_current_user();

		if ( ! ( $current_user instanceof WP_User ) ) {
			return;
		}

		// Todo: Make this actually work on button click ...
		if ( "activate" === $toggle_state ) {
			update_user_meta( $current_user->ID, 'hm_menu_active', false );
		} else {
			update_user_meta( $current_user->ID, 'hm_menu_active', true );
		}

		exit;
	}

	/**
	 * Load the menu only for users that have it activated.
	 */
	public function hm_load_menu( $hook ) {

		// See if the current user has the menu active
		$current_user_has_menu = get_user_option( 'hm_menu_active', get_current_user_id() );

		if ( false === $current_user_has_menu ) {
			add_action( 'admin_notices', array( $this, 'hm_notice_not_active' ) );
			return;
		}

		// Load it for the admin page
		add_action( 'admin_enqueue_scripts', array( $this, 'hm_add_admin_CSS' ) );

 		// add scripts and styles only available in admin
		add_action( 'admin_enqueue_scripts', array( $this, 'hm_add_admin_JS' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'hm_create_menu' ) );
	}

	/**
	 * Show that the menu plugin is not activated for the current user. While
	 * the plugin is active, it is not working for all users, just for the ones
	 * that want it.
	 */
	public function hm_notice_not_active() {
		$current_user = wp_get_current_user();

		if ( ! ( $current_user instanceof WP_User ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible">
			<p><?php _e( 'Hey, ' . $current_user->display_name . '! <a href="#" class="">Enable Horizontal Menu</a> to move the left menu bar to the top. This option is only for you :) ', 'horizontalmenu' ); ?></p>
		</div>
		<?php
	}

	public function hm_create_menu( $hook ) {
		require_once( HM_PATH . '/inc/admin-menu.php' );
	}

	/**
	 *
	 * Adding JavaScript scripts
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function hm_add_JS() {
		wp_enqueue_script( 'jquery' );
		// load custom JSes and put them in footer
		wp_register_script( 'samplescript', plugins_url( '/js/samplescript.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'samplescript' );
	}


	/**
	 *
	 * Adding JavaScript scripts for the admin pages only
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function hm_add_admin_JS( $hook ) {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'samplescript-admin', plugins_url( '/js/samplescript-admin.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'samplescript-admin' );
	}

	/**
	 *
	 * Add CSS styles
	 *
	 */
	public function hm_add_CSS() {
		wp_register_style( 'public', plugins_url( '/css/public.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'public' );
	}

	/**
	 *
	 * Add admin CSS styles - available only on admin
	 *
	 */
	public function hm_add_admin_CSS( $hook ) {
		wp_register_style( 'admin', plugins_url( '/css/admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'admin' );

		// Gonna keep them in the same file for now...
		//
		// if( 'toplevel_page_dx-plugin-base' === $hook ) {
		// 	wp_register_style('hm_help_page',  plugins_url( '/help-page.css', __FILE__ ) );
		// 	wp_enqueue_style('hm_help_page');
		// }
	}

	/**
	 *
	 * Callback for registering pages
	 *
	 * This demo registers a custom page for the plugin and a subpage
	 *
	 */
	public function hm_admin_pages_callback() {
		add_menu_page(__( "Horizontal Menu Admin", 'hotizontalmenu' ), __( "Horizontal Menu Admin", 'hotizontalmenu' ), 'edit_posts', 'horizontalmenu', array( $this, 'Horizontal_Menu' ) );
		// add_submenu_page( 'dx-plugin-base', __( "Base Subpage", 'hotizontalmenu' ), __( "Base Subpage", 'hotizontalmenu' ), 'edit_themes', 'dx-base-subpage', array( $this, 'hm_plugin_subpage' ) );
		// add_submenu_page( 'dx-plugin-base', __( "Remote Subpage", 'hotizontalmenu' ), __( "Remote Subpage", 'hotizontalmenu' ), 'edit_themes', 'dx-remote-subpage', array( $this, 'hm_plugin_side_access_page' ) );
	}

	/**
	 *
	 * The content of the base page
	 *
	 */
	public function Horizontal_Menu() {
		include_once( HM_PATH_INCLUDES . '/base-page-template.php' );
	}

	public function hm_plugin_side_access_page() {
		include_once( HM_PATH_INCLUDES . '/remote-page-template.php' );
	}

	/**
	 *
	 * The content of the subpage
	 *
	 * Use some default UI from WordPress guidelines echoed here (the sample above is with a template)
	 *
	 * @see http://www.onextrapixel.com/2009/07/01/how-to-design-and-style-your-wordpress-plugin-admin-panel/
	 *
	 */
	public function hm_plugin_subpage() {
		echo '<div class="wrap">';
		_e( "<h2>DX Plugin Subpage</h2> ", 'hotizontalmenu' );
		_e( "I'm a subpage and I know it!", 'hotizontalmenu' );
		echo '</div>';
	}

	/**
	 *
	 *  Adding right and bottom meta boxes to Pages
	 *
	 */
	public function hm_meta_boxes_callback() {
		// register side box
		add_meta_box(
			'hm_side_meta_box',
			__( "DX Side Box", 'hotizontalmenu' ),
			array( $this, 'hm_side_meta_box' ),
		        'pluginbase', // leave empty quotes as '' if you want it on all custom post add/edit screens
		        'side',
		        'high'
		        );

		// register bottom box
		add_meta_box(
			'hm_bottom_meta_box',
			__( "DX Bottom Box", 'hotizontalmenu' ),
			array( $this, 'hm_bottom_meta_box' ),
		    	'' // leave empty quotes as '' if you want it on all custom post add/edit screens or add a post type slug
		    	);
	}

	/**
	 *
	 * Init right side meta box here
	 * @param post $post the post object of the given page
	 * @param metabox $metabox metabox data
	 */
	public function hm_side_meta_box( $post, $metabox) {
		_e("<p>Side meta content here</p>", 'hotizontalmenu');

		// Add some test data here - a custom field, that is
		$hm_test_input = '';
		if ( ! empty ( $post ) ) {
			// Read the database record if we've saved that before
			$hm_test_input = get_post_meta( $post->ID, 'hm_test_input', true );
		}
		?>
		<label for="dx-test-input"><?php _e( 'Test Custom Field', 'hotizontalmenu' ); ?></label>
		<input type="text" id="dx-test-input" name="hm_test_input" value="<?php echo $hm_test_input; ?>" />
		<?php
	}

	/**
	 * Save the custom field from the side metabox
	 * @param $post_id the current post ID
	 * @return post_id the post ID from the input arguments
	 *
	 */
	public function hm_save_sample_field( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$slug = 'pluginbase';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}

		// If the custom field is found, update the postmeta record
		// Also, filter the HTML just to be safe
		if ( isset( $_POST['hm_test_input']  ) ) {
			update_post_meta( $post_id, 'hm_test_input',  esc_html( $_POST['hm_test_input'] ) );
		}
	}

	/**
	 *
	 * Init bottom meta box here
	 * @param post $post the post object of the given page
	 * @param metabox $metabox metabox data
	 */
	public function hm_bottom_meta_box( $post, $metabox) {
		_e( "<p>Bottom meta content here</p>", 'hotizontalmenu' );
	}

	/**
	 * Initialize the Settings class
	 *
	 * Register a settings section with a field for a secure WordPress admin option creation.
	 *
	 */
	public function hm_register_settings() {
		require_once( HM_PATH . '/plugin-settings.class.php' );
		new HM_Plugin_Settings();
	}

	/**
	 * Register a sample shortcode to be used
	 *
	 * First parameter is the shortcode name, would be used like: [dxsampcode]
	 *
	 */
	public function hm_sample_shortcode() {
		add_shortcode( 'dxsampcode', array( $this, 'hm_sample_shortcode_body' ) );
	}

	/**
	 * Returns the content of the sample shortcode, like [dxsamplcode]
	 * @param array $attr arguments passed to array, like [dxsamcode attr1="one" attr2="two"]
	 * @param string $content optional, could be used for a content to be wrapped, such as [dxsamcode]somecontnet[/dxsamcode]
	 */
	public function hm_sample_shortcode_body( $attr, $content = null ) {
		/*
		 * Manage the attributes and the content as per your request and return the result
		 */
		return __( 'Sample Output', 'hotizontalmenu');
	}

	/**
	 * Hook for including a sample widget with options
	 */
	public function hm_sample_widget() {
		include_once HM_PATH_INCLUDES . '/dx-sample-widget.class.php';
	}

	/**
	 * Add textdomain for plugin
	 */
	public function hm_add_textdomain() {
		load_plugin_textdomain( 'hotizontalmenu', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Callback for saving a simple AJAX option with no page reload
	 */
	public function store_ajax_value() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['hm_option_from_ajax'] ) ) {
			update_option( 'hm_option_from_ajax' , $_POST['data']['hm_option_from_ajax'] );
		}
		die();
	}

	/**
	 * Callback for getting a URL and fetching it's content in the admin page
	 */
	public function fetch_ajax_url_http() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['hm_url_for_ajax'] ) ) {
			$ajax_url = $_POST['data']['hm_url_for_ajax'];

			$response = wp_remote_get( $ajax_url );

			if( is_wp_error( $response ) ) {
				echo json_encode( __( 'Invalid HTTP resource', 'hotizontalmenu' ) );
				die();
			}

			if( isset( $response['body'] ) ) {
				if( preg_match( '/<title>(.*)<\/title>/', $response['body'], $matches ) ) {
					echo json_encode( $matches[1] );
					die();
				}
			}
		}
		echo json_encode( __( 'No title found or site was not fetched properly', 'hotizontalmenu' ) );
		die();
	}

	// function

}


/**
 * Register activation hook
 *
 */
function hm_on_activate_callback() {
	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User ) ) {
		return;
	}

	update_user_meta( $current_user->ID, 'hm_menu_active', true );
}

/**
 * Register deactivation hook
 *
 */
function hm_on_deactivate_callback() {
	// do something when deactivated
}

// Initialize everything
$Horizontal_Menu = new Horizontal_Menu();
