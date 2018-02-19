<?php
defined( 'ABSPATH' ) or die( 'You cannot access this page directly.' );

class HM_Plugin_Settings {

	private $hm_setting;
	/**
	 * Construct me
	 */
	public function __construct() {
		$this->hm_setting = get_option( 'hm_setting', '' );

		// register the checkbox
		add_action('admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Setup the settings
	 *
	 * Add a single checkbox setting for Active/Inactive and a text field
	 * just for the sake of our demo
	 *
	 */
	public function register_settings() {
		register_setting( 'hm_setting', 'hm_setting', array( $this, 'hm_validate_settings' ) );
	}

	public function hm_settings_callback() {
		echo _e( "Click to enable the Horizontal Menu plugin for yourself: ", 'hotizontalmenu' );
	}

	/**
	 * Helper Settings function if you need a setting from the outside.
	 *
	 * Keep in mind that in our demo the Settings class is initialized in a specific environment and if you
	 * want to make use of this function, you should initialize it earlier (before the base class)
	 *
	 * @return boolean is enabled
	 */
	public function is_enabled() {
		if ( ! empty( $this->hm_setting ) && isset ( $this->hm_setting['hm_opt_in'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Validate Settings
	 *
	 * Filter the submitted data as per your request and return the array
	 *
	 * @param array $input
	 */
	public function hm_validate_settings( $input ) {

		return $input;
	}
}
