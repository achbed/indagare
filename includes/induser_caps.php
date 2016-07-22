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


	// If we don't have an old-style user session, use what we have.
	if ( ! \indagare\users\User::hasUserSession() )
		return $allcaps;

	// Get the Member role.
	$role = get_role( 'basic' );

	// Fail back to subscriber if the Member role is not defined
	if ( is_null( $role ) )
		$role = get_role( 'subscriber' );

	return $role->capabilities;
}

add_filter( 'user_has_cap', 'ind_olduser_cap_filter', 1, 3 );

/**
 * Determines if the current session is logged in via either WP or old-style session.
 * @return boolean
 */
function ind_logged_in() {
	if ( is_user_logged_in() ) {
		return true;
	}

	return \indagare\users\User::hasUserSession();
}
