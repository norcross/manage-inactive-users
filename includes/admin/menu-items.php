<?php
/**
 * Handle the cleanup and organization of the admin sidebar.
 *
 * @package ManageInactiveUsers
 */

// Declare our namespace.
namespace NorcrossPlugins\ManageInactiveUsers\Admin\MenuItems;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;
use NorcrossPlugins\ManageInactiveUsers\Helpers as Helpers;
use NorcrossPlugins\ManageInactiveUsers\Utilities as Utilities;
use NorcrossPlugins\ManageInactiveUsers\Admin\Markup as AdminMarkup;

/**
 * Start our engines.
 */
add_action( 'admin_menu', __NAMESPACE__ . '\add_user_manage_menu' );

/**
 * Add our submenu item to use primary user menu.
 *
 * @return void
 */
function add_user_manage_menu() {
	add_users_page(
		__( 'Manage Inactive Users', 'manage-inactive-users' ),
		__( 'Inactive Users', 'manage-inactive-users' ),
		'promote_users',
		Core\MENU_ROOT,
		__NAMESPACE__ . '\render_user_manage_page'
	);
}

/**
 * Render the user manangement page.
 *
 * @return HTML
 */
function render_user_manage_page() {

	// Bail without use capabilites.
	if ( ! current_user_can( 'promote_users' ) ) {
		return;
	}

	// Get our flag for pending setups.
	$maybe_is_pending   = Helpers\maybe_has_pending();

	// Get my form link.
	$form_action_link   = Utilities\get_admin_menu_link();

	// Handle the opening div.
	echo '<div class="wrap miu-admin-settings-page-wrap">';

		// Handle our admin intro.
		AdminMarkup\display_admin_page_intro();

		// Wrap the actual form.
		echo '<form class="miu-admin-settings-form" method="post" action="' . esc_url( $form_action_link ) . '">';

			// Display the screen for the pending data if we have it.
			if ( ! empty( $maybe_is_pending ) ) {

				// Render the list.
				AdminMarkup\display_pending_users_list_fields( $maybe_is_pending );

				// Display the submit.
				AdminMarkup\display_pending_users_submit_fields();

			} else {

				// Display the user criteria fields.
				AdminMarkup\display_user_criteria_fields();

				// Display the submit.
				AdminMarkup\display_user_criteria_submit_fields();
			}

		// Close up my form.
		echo '</form>';

	// Close up the div wrapper.
	echo '</div>';
}
