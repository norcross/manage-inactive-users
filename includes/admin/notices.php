<?php
/**
 * Handle any admin notices.
 *
 * @package ManageInactiveUsers
 */

// Declare our namespace.
namespace NorcrossPlugins\ManageInactiveUsers\Admin\Notices;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;
use NorcrossPlugins\ManageInactiveUsers\Helpers as Helpers;
use NorcrossPlugins\ManageInactiveUsers\Utilities as Utilities;
use NorcrossPlugins\ManageInactiveUsers\Admin\Markup as AdminMarkup;

/**
 * Start our engines.
 */
add_action( 'admin_notices', __NAMESPACE__ . '\display_admin_notices' );

/**
 * Display our admin notices.
 *
 * @return void
 */
function display_admin_notices() {

	// Make sure we have the completed flags.
	if ( empty( $_GET['miu-admin-action-complete'] ) || empty( $_GET['miu-admin-action-result'] ) ) {
		return;
	}

	// Determine the message type.
	$result_type    = ! empty( $_GET['miu-admin-success'] ) ? 'success' : 'error';

	// Handle dealing with an error return.
	if ( 'error' === $result_type ) {

		// Figure out my error code.
		$error_code = ! empty( $_GET['miu-admin-error-code'] ) ? $_GET['miu-admin-error-code'] : 'unknown';

		// Handle my error text retrieval.
		$error_text = Helpers\get_error_notice_text( $error_code );

		// Make sure the error type is correct, since one is more informational.
		$error_type = 'NO-INACTIVE-USERS' === $error_code ? 'info' : 'error';

		// And handle the display.
		AdminMarkup\display_admin_notice_markup( $error_text, $error_type );

		// And be done.
		return;
	}

	// Handle my success message based on the clear flag.
	if ( 'cleared' === sanitize_text_field( $_GET['miu-admin-action-result'] ) ) {
		$alert_text = __( 'Success! The pending data has been cleared.', 'manage-inactive-users' );
	} else {
		$alert_text = __( 'Success! The selected users have been updated to Subscriber status.', 'manage-inactive-users' );
	}

	// And handle the display.
	AdminMarkup\display_admin_notice_markup( $alert_text, 'success' );

	// And be done.
	return;
}
