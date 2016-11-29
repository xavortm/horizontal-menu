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

		$("#hm-adminmenumain #adminmenu > li.menu-top").each(function() {
			menuItemsWidth += $(this).width();
			menuItemsObj[menuItemsWidth] = $(this);
		});

		console.log("menuItemsWidth returned from calculateMenuItemsWidth() == " + menuItemsWidth);

		return menuItemsWidth;
	}

	function calculateAllowedArea() {
		var showMoreWidth = $(".hm-show-more").width() + 150;
		var windowWidth = $(window).width();

		console.log("Allowed area returned from calculateAllowedArea() == " + (windowWidth - showMoreWidth) );

		return windowWidth - showMoreWidth;
	}

	function responsiveMenuDropdown() {
		var allowedMenuWIdth = calculateAllowedArea();
		var remapMenu = false;
		var itemsToRemove = 0;

		var loop = 0;
		$.each(menuItemsObj, function(key, value) {
			if (key >= allowedMenuWIdth) {
				menuItemsDropdownObj[key] = value;
				menuItemsObj[key] = null;
				itemsToRemove++;
			}
			console.log(loop++);
		});

		console.log("Items to remove == " + itemsToRemove);

		if(itemsToRemove == 0) {
			$(".hm-show-more").hide();
			$("#hm-adminmenumain .sm-more-dropdown > li").slice( -itemsToRemove ).addClass('is-visible');
			$("#hm-adminmenumain #adminmenu > li").each(function(){ $(this).addClass('is-visible') });
		} else {
			$(".hm-show-more").show();
			$("#hm-adminmenumain #adminmenu > li").each(function(){ $(this).addClass('is-visible') });
			$("#hm-adminmenumain .sm-more-dropdown > li").each(function(){ $(this).removeClass('is-visible') });

			$("#hm-adminmenumain #adminmenu > li").slice( -itemsToRemove ).removeClass('is-visible');
			$("#hm-adminmenumain .sm-more-dropdown > li").slice( -itemsToRemove ).addClass('is-visible');
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
