<?php

/**
 * ind_olduser_cap_filter()
 *
 * Filter on the current_user_can() function.
 * This function is used to explicitly allow old-style users to
 * access permission-based content.
 *
 * @param array $allcaps All the capabilities of the user
 * @param array $cap     [0] Required capability
 * @param array $args    [0] Requested capability
 *                       [1] User ID
 *                       [2] Associated object ID
 */
function ind_olduser_cap_filter( $allcaps, $cap, $args ) {
	// Load up the old-style user object if it doesnt already exist
	require_once get_stylesheet_directory().'/app/lib/user.php';

	// If we're already logged in using WP techniques, use what we have.
	if ( is_user_logged_in() )
		return $allcaps;

	// Get the subscriber role.
	$role = get_role( 'subscriber' );
	return $role->capabilities;
}

add_filter( 'user_has_cap', 'ind_olduser_cap_filter', 1, 3 );

/**
 * Determines if the current session is logged in via either WP or old-style session.
 * @return boolean
 */
function ind_logged_in() {
	return is_user_logged_in();
}

if( !current_user_can('edit_posts') ) {
	function mytheme_admin_bar_render() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'edit-profile', 'user-actions' );
	}
	add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

	function stop_access_profile() {
		if ( defined( 'IS_PROFILE_PAGE' ) && ( IS_PROFILE_PAGE === true ) ) {
			wp_redirect( '/account/' );
		}
		remove_menu_page( 'profile.php' );
		remove_submenu_page( 'users.php', 'profile.php' );
	}
	add_action( 'admin_init', 'stop_access_profile' );
}

