/* sample script here */

jQuery(document).ready(function($) {

	// Handle the AJAX field save action
	$('#dx-plugin-base-ajax-form').on('submit', function(e) {
		e.preventDefault();

		var ajax_field_value = $('#dx_option_from_ajax').val();

		 $.post(ajaxurl, {
			 	data: { 'dx_option_from_ajax': ajax_field_value },
		             action: 'store_ajax_value'
				 }, function(status) {
					 	 $('#dx_page_messages').html('Value updated successfully');
		           }
		);
	});

	// Handle the AJAX URL fetcher
	$('#dx-plugin-base-http-form').on('submit', function(e) {
		e.preventDefault();

		var ajax_field_value = $('#dx_url_for_ajax').val();

		 $.post(ajaxurl, {
			 	data: { 'dx_url_for_ajax': ajax_field_value },
		             action: 'fetch_ajax_url_http'
				 }, function(status) {
					 	 $('#dx_page_messages').html('The URL title is fetching in the frame below');
					 	 $('#resource-window').html( '<p>Site title: ' + status + '</p>');
		           }
		);
	});


	// Responsive main menu array
	var menuItemsObj = {};
	var menuItemsDropdownObj = {};

	// Run this function only once. It will also fill in the array
	// containing all menu items
	function calculateMenuItemsWidth() {
		var menuItemsWidth = 0;

		$("#hm-adminmenumain #adminmenu > li:not(.hm-show-more)").each(function() {
			menuItemsWidth += $(this).width();
			menuItemsObj[menuItemsWidth] = $(this);
		});

		return menuItemsWidth;
	}

	function calculateAllowedArea() {
		var showMoreWidth = $(".hm-show-more").width() + 150;
		var windowWidth = $(window).width();

		return windowWidth - showMoreWidth;
	}

	function responsiveMenuDropdown() {
		var allowedMenuWIdth = calculateAllowedArea();
		var remapMenu = false;
		var itemsToRemove = 0;

		$.each(menuItemsObj, function(key, value) {
			if (key >= allowedMenuWIdth) {
				menuItemsDropdownObj[key] = value;
				menuItemsObj[key] = null;
				itemsToRemove++;
			}
		});

		if(itemsToRemove == 0) {
			$(".hm-show-more").hide();
		} else {
			$(".hm-show-more").show();
			$("#hm-adminmenumain #adminmenu > li").each(function(){ $(this).show() });
			$("#hm-adminmenumain .sm-more-dropdown > li").each(function(){ $(this).hide() });
			$("#hm-adminmenumain #adminmenu > li").slice( -itemsToRemove ).hide();
			$("#hm-adminmenumain .sm-more-dropdown > li").slice( -itemsToRemove ).show();
		}
	}

	// Calcualte on resize
	$( window ).resize(function() {
		responsiveMenuDropdown();
	});

	// Run once
	calculateMenuItemsWidth();
	responsiveMenuDropdown();
});
