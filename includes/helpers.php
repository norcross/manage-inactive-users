<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package ManageInactiveUsers
 */

// Call our namepsace.
namespace NorcrossPlugins\ManageInactiveUsers\Helpers;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;

/**
 * Get the array of each kind of date range box we have.
 *
 * @param  boolean $keys  Whether we want array keys or all of it.
 *
 * @return array
 */
function get_range_types( $keys = false ) {

	// Set an array of what we know we need.
	$ranges = array(
		DAY_IN_SECONDS   => __( 'Days', 'manage-inactive-users' ),
		WEEK_IN_SECONDS  => __( 'Weeks', 'manage-inactive-users' ),
		MONTH_IN_SECONDS => __( 'Months', 'manage-inactive-users' ),
		YEAR_IN_SECONDS  => __( 'Years', 'manage-inactive-users' ),
	);

	// Set the ranges with a filter.
	$ranges = apply_filters( Core\HOOK_PREFIX . 'range_types', $ranges );

	// Bail if we have no ranges.
	if ( empty( $ranges ) ) {
		return false;
	}

	// Return the entire thing or just the single.
	return ! empty( $keys ) ? array_keys( $ranges ) : $ranges;
}

/**
 * Get the array of each kind of user role we have.
 *
 * @param  boolean $keys  Whether we want array keys or all of it.
 *
 * @return array
 */
function get_user_roles( $keys = false ) {

	// Set an array of what we know we need.
	$roles  = array(
		'contributor' => __( 'Contributor', 'manage-inactive-users' ),
		'author'      => __( 'Author', 'manage-inactive-users' ),
		'editor'      => __( 'Editor', 'manage-inactive-users' ),
	);

	// Set the ranges with a filter.
	$roles  = apply_filters( Core\HOOK_PREFIX . 'user_roles', $roles );

	// Bail if we have no ranges.
	if ( empty( $roles ) ) {
		return false;
	}

	// Return the entire thing or just the single.
	return ! empty( $keys ) ? array_keys( $roles ) : $roles;
}

/**
 * Check an code and (usually an error) return the appropriate text.
 *
 * @param  string $return_code  The code provided.
 *
 * @return string
 */
function get_error_notice_text( $return_code = '' ) {

	// Handle my different error codes.
	switch ( esc_attr( $return_code ) ) {

		case 'NO-CRITERIA' :
			return __( 'No parameters were defined. Please review the options below and try again.', 'manage-inactive-users' );
			break;

		case 'MISSING-USER-ROLES' :
			return __( 'No user roles were selected. Please select one or more and try again.', 'manage-inactive-users' );
			break;

		case 'MISSING-DATE-INFO' :
			return __( 'Please enter both a numeric value and the range to set a date.', 'manage-inactive-users' );
			break;

		case 'NO-INACTIVE-USERS' :
			return __( 'No inactive users were found based on the selected options.', 'manage-inactive-users' );
			break;

		case 'unknown' :
		case 'unknown-error' :
			return __( 'There was an unknown error with your request.', 'manage-inactive-users' );
			break;

		default :
			return __( 'There was an error with your request.', 'manage-inactive-users' );
			break;

		// End all case breaks.
	}
}

/**
 * Figure out the date to check against.
 *
 * @param  integer $number  How many.
 * @param  integer $range   How long.
 *
 * @return mixed
 */
function calculate_date_for_query( $number = 0, $range = 0 ) {

	// Bail without the items.
	if ( empty( $number ) || empty( $range ) ) {
		return false;
	}

	// Calculate the total.
	$calc_total = absint( $number ) * absint( $range );

	// Now calculate the time since now.
	return time() - absint( $calc_total );
}

/**
 * Get the inactive users based on the IDs.
 *
 * @param  array   $user_ids        What IDs we have to check.
 * @param  integer $inactive_stamp  What the timestamp for inactive is.
 *
 * @return array
 */
function get_inactive_user_ids( $user_ids = array(), $inactive_stamp = 0 ) {

	// Bail without the items.
	if ( empty( $user_ids ) || empty( $inactive_stamp ) ) {
		return false;
	}

	// Set an empty for converting.
	$set_inactive_array = array();

	// Set the global.
	global $wpdb;

	// Set our table name.
	$table_name = $wpdb->posts;

	// Loop using the ID.
	foreach ( $user_ids as $user_id ) {

		// Set up our query.
		$query_args = $wpdb->prepare("
			SELECT   post_date
			FROM     $table_name
			WHERE    post_author = '%d'
			ORDER BY post_date DESC
		", absint( $user_id ) );

		// Process the query.
		$query_run  = $wpdb->get_var( $query_args );
		// preprint( $query_run, true );

		// If they have no date, they are very inactive.
		if ( empty( $query_run ) ) {
			$set_inactive_array[] = $user_id;
		}

		// If a date exists, now we compare.
		if ( ! empty( $query_run ) ) {

			// Handle my post stamp.
			$post_stamp = strtotime( $query_run );

			// Now check if the date is more than a certain length.
			if ( absint( $post_stamp ) > absint( $inactive_stamp ) ) {
				continue;
			}

			// Add them to the array of IDs.
			$set_inactive_array[] = $user_id;
		}

		// Nothing left inside the loop.
	}

	// Return the array.
	return $set_inactive_array;
}

/**
 * Store the data we have to swap.
 *
 * @param  array   $user_ids        What IDs we have to update.
 * @param  integer $inactive_stamp  What the timestamp used.
 *
 * @return void
 */
function set_pending_user_ids( $user_ids = array(), $inactive_stamp = 0  ) {

	// Set a data array.
	$setup_pending_data = array(
		'count' => count( $user_ids ),
		'stamp' => absint( $inactive_stamp ),
		'users' => $user_ids,
	);

	// Store the IDs as an option and then redirect.
	update_option( Core\OPTION_PREFIX . 'inactives', $setup_pending_data, 'no' );
}

/**
 * Check to see if we have pending data.
 *
 * @param  boolean $return_ids  Whether to return just the user IDs.
 *
 * @return mixed
 */
function maybe_has_pending( $return_ids = false ) {

	// Check for the data.
	$maybe_has_data = get_option( Core\OPTION_PREFIX . 'inactives' );

	// If it's empty or malformed, return false.
	if ( empty( $maybe_has_data ) || ! is_array( $maybe_has_data ) ) {
		return false;
	}

	// Return either the data, or just the IDs.
	return false !== $return_ids ? $maybe_has_data['users'] : $maybe_has_data;
}

/**
 * Clear out any pending data we have.
 *
 * @param  boolean $include_last  Including the last run option too.
 *
 * @return void
 */
function clear_pending_data( $include_last = true ) {

	// Delete the pending data.
	delete_option( Core\OPTION_PREFIX . 'inactives' );

	// Include the last run if need be.
	if ( false !== $include_last ) {
		delete_option( Core\OPTION_PREFIX . 'last_run' );
	}

	// Nothing else to clear.
}
