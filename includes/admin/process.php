<?php
/**
 * Handle the processing involves.
 *
 * @package ManageInactiveUsers
 */

// Declare our namespace.
namespace NorcrossPlugins\ManageInactiveUsers\Admin\Process;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;
use NorcrossPlugins\ManageInactiveUsers\Utilities as Utilities;
use NorcrossPlugins\ManageInactiveUsers\Helpers as Helpers;

/**
 * Start our engines.
 */
add_action( 'admin_init', __NAMESPACE__ . '\run_criteria_lookup' );
add_action( 'admin_init', __NAMESPACE__ . '\run_pending_data_clear' );
add_action( 'admin_init', __NAMESPACE__ . '\run_pending_user_updates' );

/**
 * Load any admin CSS or JS as needed.
 *
 * @return void
 */
function run_criteria_lookup() {

	// First check for the POST variable.
	if ( ! isset( $_POST['miu-admin-criteria-submit'] ) || 'go' !== sanitize_text_field( $_POST['miu-admin-criteria-submit'] ) ) {
		return;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST[ Core\NONCE_PREFIX . 'criteria_set' ] ) || ! wp_verify_nonce( $_POST[ Core\NONCE_PREFIX . 'criteria_set' ], Core\NONCE_PREFIX . 'criteria_submit' ) ) {
		wp_die( __( 'The security nonce did not validate. Please try again later.', 'manage-inactive-users' ) );
	}

	// Error out with nothing.
	if ( empty( $_POST['miu-criteria-settings'] ) ) {
		Utilities\redirect_admin_action_result( 'NO-CRITERIA' );
	}

	// Set the variable.
	$user_criteria  = $_POST['miu-criteria-settings'];

	// Error out with the date items missing.
	if ( empty( $user_criteria['roles'] ) ) {
		Utilities\redirect_admin_action_result( 'MISSING-USER-ROLES' );
	}

	// Error out with the date items missing.
	if ( empty( $user_criteria['number'] ) || empty( $user_criteria['range'] ) ) {
		Utilities\redirect_admin_action_result( 'MISSING-DATE-INFO' );
	}

	// Make sure the array is clean.
	$set_role_array = array_map( 'sanitize_text_field', $user_criteria['roles'] );

	// Get the timestamp we need based on the range.
	$inactive_stamp = Helpers\calculate_date_for_query( $user_criteria['number'], $user_criteria['range'] );

	// Build the arguments for the users.
	$user_lookup_args   = array(
		'fields'   => 'ids',
		'role__in' => $set_role_array,
		'number'   => -1
	);

	// Pull my contributors list.
	$build_user_array   = get_users( $user_lookup_args );

	// Now do the lookup.
	$maybe_has_inactive = Helpers\get_inactive_user_ids( $build_user_array, $inactive_stamp );

	// Error out with no users to convert.
	if ( empty( $maybe_has_inactive ) ) {
		Utilities\redirect_admin_action_result( 'NO-INACTIVE-USERS' );
	}

	// Store the relevant data.
	Helpers\set_pending_user_ids( $maybe_has_inactive, $inactive_stamp );

	// And redirect with our good one.
	Utilities\redirect_admin_pending_status();
}

/**
 * Clear the pending data if we requested it.
 *
 * @return void
 */
function run_pending_data_clear() {

	// First check for the POST variable.
	if ( ! isset( $_POST['miu-admin-pending-clear'] ) || 'go' !== sanitize_text_field( $_POST['miu-admin-pending-clear'] ) ) {
		return;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST[ Core\NONCE_PREFIX . 'pending_set' ] ) || ! wp_verify_nonce( $_POST[ Core\NONCE_PREFIX . 'pending_set' ], Core\NONCE_PREFIX . 'pending_submit' ) ) {
		wp_die( __( 'The security nonce did not validate. Please try again later.', 'manage-inactive-users' ) );
	}

	// Delete the data.
	Helpers\clear_pending_data();

	// And redirect with the success flag.
	Utilities\redirect_admin_action_result( '', 'cleared', true );
}

/**
 * Handle the actual pending user actions.
 *
 * @return void
 */
function run_pending_user_updates() {

	// First check for the POST variable.
	if ( ! isset( $_POST['miu-admin-pending-submit'] ) || 'go' !== sanitize_text_field( $_POST['miu-admin-pending-submit'] ) ) {
		return;
	}

	// Check to see if our nonce was provided.
	if ( empty( $_POST[ Core\NONCE_PREFIX . 'pending_set' ] ) || ! wp_verify_nonce( $_POST[ Core\NONCE_PREFIX . 'pending_set' ], Core\NONCE_PREFIX . 'pending_submit' ) ) {
		wp_die( __( 'The security nonce did not validate. Please try again later.', 'manage-inactive-users' ) );
	}

	// Fetch the user IDs we have stored.
	$get_pending_users  = Helpers\maybe_has_pending( true );

	// Error out with no users to convert.
	if ( empty( $get_pending_users ) ) {
		Utilities\redirect_admin_action_result( 'NO-INACTIVE-USERS' );
	}

	// Loop our user IDs and update them.
	foreach ( $get_pending_users as $user_id ) {

		// Fetch the WP_User object of our user.
		$get_user_obj   = new \WP_User( absint( $user_id ) );

		// Replace the current role with 'subscriber' role.
		$get_user_obj->set_role( 'subscriber' );
	}

	// Delete the data.
	Helpers\clear_pending_data( false );

	// Set our last run timestamp.
	update_option( Core\OPTION_PREFIX . 'last_run', time(), 'no' );

	// And redirect with the success flag.
	Utilities\redirect_admin_action_result( '', 'updated', true );
}



