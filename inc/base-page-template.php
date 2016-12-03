
<div class="wrap">
	<h2><?php _e( "Horizontal menus settings page", 'horizontalmenu' ); ?></h2>

	<p><?php _e( "Setup the way you want your dashboard to look like.", 'hotizontalmenu' ); ?></p>

	<?php
	$button_value = __("Deactivate", "horizontalmenu");
	$button_class = "deactivate-horizontal-menu";

	// See if the current user has the menu active
	$current_user_has_menu = get_user_option( 'hm_menu_active', get_current_user_id() );

	if ( false === $current_user_has_menu ) {
		$button_value = __("Activate", "horizontalmenu");
		$button_class = "activate-horizontal-menu";
	}

	?>

	<span class="horizontal-menu-toggle activate-horizontal-menu button button-primary"><?php _e( $button_value, 'hotizontalmenu' ); ?></span>
</div>
