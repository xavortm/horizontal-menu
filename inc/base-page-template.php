<?php
defined( 'ABSPATH' ) or die( 'You cannot access this page directly.' );
?>
<div class="wrap">
	<h2><?php _e( "Horizontal menus settings page", 'horizontalmenu' ); ?></h2>

	<p><?php _e( "Setup the way you want your dashboard to look like.", 'hotizontalmenu' ); ?></p>

	<?php
	$button_value = __("Disable", "horizontalmenu");
	$button_class = "deactivate-horizontal-menu";

	// See if the current user has the menu active
	$current_user_has_menu = get_user_option( 'hm_menu_active', get_current_user_id() );

	if ( false === (boolean) $current_user_has_menu ) {
		$button_value = __("Enable", "horizontalmenu");
		$button_class = "activate-horizontal-menu";
	}

	?>
	<form name="form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" novalidate="">
		<input name="toggle" type="hidden" value="<?php echo strtolower($button_value); ?>">
		<input name="action" type="hidden" value="save_hm_toggle">
		<button type="submit" name="Submit" class="horizontal-menu-toggle activate-horizontal-menu button button-primary"/><?php _e( $button_value, 'hotizontalmenu' ); ?></button>
	</form>
	<!-- <span class="horizontal-menu-toggle activate-horizontal-menu button button-primary"><?php _e( $button_value, 'hotizontalmenu' ); ?></span> -->
</div>
