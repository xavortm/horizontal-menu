<div class="wrap">
	<h2><?php _e( "Horizontal menus settings page", 'hotizontalmenu' ); ?></h2>

	<p><?php _e( "Setup the way you want your dashboard to look like.", 'hotizontalmenu' ); ?></p>

	<form id="dx-plugin-base-form" action="options.php" method="POST">

			<?php settings_fields( 'hm_setting' ) ?>
			<?php do_settings_sections( 'dx-plugin-base' ) ?>

			<input type="submit" value="<?php _e( "Save", 'hotizontalmenu' ); ?>" />
	</form> <!-- end of #dxtemplate-form -->
</div>
