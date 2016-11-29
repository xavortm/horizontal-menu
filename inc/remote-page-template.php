<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-base-template"><br></div>
	<h2><?php _e( "Remote plugin page", 'hotizontalmenu' ); ?></h2>

	<p><?php _e( "Performing side activities - AJAX and HTTP fetch", 'hotizontalmenu' ); ?></p>
	<div id="hm_page_messages"></div>

	<?php
		$hm_ajax_value = get_option( 'hm_option_from_ajax', '' );
	?>

	<h3><?php _e( 'Store a Database option with AJAX', 'hotizontalmenu' ); ?></h3>
	<form id="dx-plugin-base-ajax-form" action="options.php" method="POST">
			<input type="text" id="hm_option_from_ajax" name="hm_option_from_ajax" value="<?php echo $hm_ajax_value; ?>" />

			<input type="submit" value="<?php _e( "Save with AJAX", 'hotizontalmenu' ); ?>" />
	</form> <!-- end of #dx-plugin-base-ajax-form -->

	<h3><?php _e( 'Fetch a title from URL with HTTP call through AJAX', 'hotizontalmenu' ); ?></h3>
	<form id="dx-plugin-base-http-form" action="options.php" method="POST">
			<input type="text" id="hm_url_for_ajax" name="hm_url_for_ajax" value="http://wordpress.org" />

			<input type="submit" value="<?php _e( "Fetch URL title with AJAX", 'hotizontalmenu' ); ?>" />
	</form> <!-- end of #dx-plugin-base-http-form -->

	<div id="resource-window">
	</div>

</div>
