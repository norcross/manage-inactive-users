<?php
/**
 * Our utility functions to use across the plugin.
 *
 * @package ManageInactiveUsers
 */

// Call our namepsace.
namespace NorcrossPlugins\ManageInactiveUsers\Utilities;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;

/**
 * Do the whole 'check current screen' progressions.
 *
 * @param  boolean $ajax      Whether to also bail on an Ajax call.
 * @param  string  $return    How to return the result. Usually boolean.
 *
 * @return boolean|object   Whether or not we are.
 */
function check_admin_screen( $ajax = false, $return = 'boolean' ) {

	// Do the Ajax check first.
	if ( ! empty( $ajax ) && wp_doing_ajax() ) {
		return false;
	}

	// Bail if not on admin or our function doesnt exist.
	if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	// Get my current screen.
	$screen = get_current_screen();

	// Bail without.
	if ( empty( $screen ) || ! is_object( $screen ) ) {
		return false;
	}

	// Make sure the base exists and then check it.
	if ( empty( $screen->base ) || 'users_page_' . Core\MENU_ROOT !== sanitize_text_field( $screen->base ) ) {
		return false;
	}

	// Nothing left. We passed.
	return 'screen' === sanitize_text_field( $return ) ? $screen : true;
}

/**
 * Fetch the admin menu link on the tools menu.
 *
 * @return string
 */
function get_admin_menu_link() {

	// Bail if we aren't on the admin side.
	if ( ! is_admin() ) {
		return false;
	}

	// Set the root menu page and the admin base.
	$set_menu_root  = trim( Core\MENU_ROOT );

	// If we're doing Ajax, build it manually.
	if ( wp_doing_ajax() ) {
		return add_query_arg( array( 'page' => $set_menu_root ), admin_url( 'users.php' ) );
	}

	// Use the `menu_page_url` function if we have it.
	if ( function_exists( 'menu_page_url' ) ) {

		// Return using the function.
		return menu_page_url( $set_menu_root, false );
	}

	// Build out the link if we don't have our function.
	return add_query_arg( array( 'page' => $set_menu_root ), admin_url( 'users.php' ) );
}

/**
 * Redirect based on an edit action result.
 *
 * @param  string  $error    Optional error code.
 * @param  string  $result   What the result of the action was.
 * @param  boolean $success  Whether it was successful.
 *
 * @return void
 */
function redirect_admin_action_result( $error = '', $result = 'failed', $success = false ) {

	// Set our base redirect link.
	$base_redirect  = get_admin_menu_link();

	// Set up my redirect args.
	$redirect_args  = array(
		'miu-admin-success'         => $success,
		'miu-admin-action-complete' => 1,
		'miu-admin-action-result'   => esc_attr( $result ),
	);

	// Add the error code if we have one.
	$redirect_args  = ! empty( $error ) ? wp_parse_args( $redirect_args, array( 'miu-admin-error-code' => esc_attr( $error ) ) ) : $redirect_args;

	// Now set my redirect link.
	$redirect_link  = add_query_arg( $redirect_args, $base_redirect );

	// Do the redirect.
	wp_safe_redirect( $redirect_link );
	exit;
}

/**
 * Redirect just for the 2nd step of changing.
 *
 * @return void
 */
function redirect_admin_pending_status() {

	// Set our base redirect link.
	$base_redirect  = get_admin_menu_link();

	// Now set my redirect link.
	$redirect_link  = add_query_arg( array( 'miu-admin-status' => 'pending' ), $base_redirect );

	// Do the redirect.
	wp_safe_redirect( $redirect_link );
	exit;
}
