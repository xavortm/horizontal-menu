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

	/**
	 * Advanced TODO:
	 * --------------
	 * If the menu has no submenus add it as submenu to the first item in the dropdown menu.
	 * Meaning, all items with no dropdowns that are inside the "more" dropdown are listed on the left,
	 * and all that do have dropdowns are listed after that just as they are right now.
	 */


	// Responsive main menu array
	var menuItemsObj = {};
	var menuItemsDropdownObj = {};

	/**
	 * Run this function only once. It will also fill in the array
	 * containing all menu items
	 */
	function calculateMenuItemsWidth() {
		var menuItemsWidth = 0;

		$("#hm-adminmenumain #adminmenu > li.menu-top").each(function() {
			menuItemsWidth += $(this).width();
			menuItemsObj[menuItemsWidth] = $(this);
		});

		return menuItemsWidth;
	}

	/**
	 * What is the area allowed for visible menu items. The number 150
	 * is the "buffer" to make sure verything runs smoothly.
	 *
	 * @return {int} the width in pixels allowed
	 */
	function calculateAllowedArea() {
		var showMoreWidth = $(".hm-show-more").width() + 150;
		var windowWidth = $(window).width();

		return windowWidth - showMoreWidth;
	}

	/**
	 * This is the function that is being run on screen resize.
	 */
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

		// TODO: Refactor this bit
		if(itemsToRemove == 0) {
			$(".hm-show-more").hide();
			$("#hm-adminmenumain .sm-more-dropdown > li").slice( -itemsToRemove ).addClass('is-visible');
			$("#hm-adminmenumain #adminmenu > li").each(function(){ $(this).addClass('is-visible') });
		} else {
			$(".hm-show-more").show();
			$("#hm-adminmenumain #adminmenu > li").each(function(){ $(this).addClass('is-visible') });
			$("#hm-adminmenumain .sm-more-dropdown > li:not(.no-dropdown-group)").each(function(){ $(this).removeClass('is-visible') });

			$("#hm-adminmenumain #adminmenu > li").slice( -itemsToRemove ).removeClass('is-visible');
			$("#hm-adminmenumain .sm-more-dropdown > li:not(.no-dropdown-group)").slice( -itemsToRemove ).addClass('is-visible');
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
