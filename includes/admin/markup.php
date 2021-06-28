<?php
/**
 * Set up and render the markup pieces.
 *
 * @package ManageInactiveUsers
 */

// Call our namepsace.
namespace NorcrossPlugins\ManageInactiveUsers\Admin\Markup;

// Set our alias items.
use NorcrossPlugins\ManageInactiveUsers as Core;
use NorcrossPlugins\ManageInactiveUsers\Helpers as Helpers;
use NorcrossPlugins\ManageInactiveUsers\Utilities as Utilities;

/**
 * Handle fetching and using the introduction data.
 *
 * @param  boolean $echo  Whether to echo or just return it.
 *
 * @return HTML
 */
function display_admin_page_intro( $echo = true ) {

	// Check to see if we have a last run.
	$maybe_last_run = get_option( Core\OPTION_PREFIX . 'last_run' );

	// Set an empty.
	$build  = '';

	// Start with a div.
	$build .= '<div class="miu-admin-settings-section-wrap miu-admin-settings-intro-wrap">';

		// Display the headline if we have one.
		$build .= '<h1 class="miu-admin-settings-intro-headline">' . esc_html( get_admin_page_title() ) . '</h1>';

		// And some intro content.
		if ( ! empty( $maybe_last_run ) ) {

			// Set the date we wanna show.
			$set_date_show  = date( get_option( 'date_format' ), $maybe_last_run );

			// And add it.
			$build .= '<p class="miu-admin-settings-intro-subtitle">' . sprintf( __( 'This process was last run on %s.', 'manage-inactive-users' ), '<strong>' . esc_attr( $set_date_show ) . '</strong>' ) . '</p>';
		}

	// Close out my div.
	$build .= '</div>';

	// Return if requested.
	if ( ! $echo ) {
		return $build;
	}

	// Echo it out.
	echo $build;
}

/**
 * Set up the options for displaying the search parameters.
 *
 * @param  boolean $echo  Whether to echo or just return it.
 *
 * @return HTML
 */
function display_user_criteria_fields( $echo = true ) {

	// Set an empty.
	$build  = '';

	// Start with a div.
	$build .= '<div class="miu-admin-settings-section-wrap miu-admin-settings-fields-wrap">';

		// Wrap this in an actual table.
		$build .= '<table class="form-table miu-admin-settings-table" role="presentation"><tbody>';

			// Set the row for the date setup.
			$build .= '<tr>';
				$build .= '<th scope="row">' . esc_html__( 'Inactive Range', 'manage-inactive-users' ) . '</th>';
				$build .= '<td>';

					// Output the range number input field.
					$build .= '<input name="miu-criteria-settings[number]" step="1" min="1" id="miu-criteria-settings-number" value="2" class="miu-admin-settings-input small-text" type="number">';

					// Output the range number select field.
					$build .= '<select name="miu-criteria-settings[range]" id="miu-criteria-settings-range" class="miu-admin-settings-input">';

					// Loop my range types to make the select field.
					foreach ( Helpers\get_range_types() as $array_type => $array_label ) {
						$build .= '<option value="' . absint( $array_type ) . '" ' . selected( YEAR_IN_SECONDS, absint( $array_type ), false ) . '>' . esc_html( $array_label ) . '</option>';
					}

					// Close the select.
					$build .= '</select>';

					// And explain what it is.
					$build .= '<span class="miu-admin-settings-description description">' . esc_html__( 'Set the time since the last published post.', 'manage-inactive-users' ) . '</span>';

				$build .= '</td>';
			$build .= '</tr>';

			// Set the table row for the use role types.
			$build .= '<tr>';

				// Handle my label.
				$build .= '<th scope="row">' . esc_html__( 'User Roles', 'manage-inactive-users' ) . '</th>';

				// Do the actual checkbox.
				$build .= '<td>';
					$build .= '<fieldset>';

						// Display the legend.
						$build .= '<legend class="screen-reader-text"><span>' . esc_html__( 'User Roles', 'manage-inactive-users' ) . '</span></legend>';

						// Loop my user roles to make the input fields.
						foreach ( Helpers\get_user_roles() as $role_type => $role_label ) {

							// Set the field ID.
							$set_field_id   = 'miu-criteria-settings-role-' . sanitize_text_field( $role_type );

							// Wrap it in a span so we can do an inline.
							$build .= '<span class="miu-admin-settings-checkbox-wrap">';

								// Wrap the input inside the label.
								$build .= '<label for="' . esc_attr( $set_field_id ) . '">';

									// Construct the checkbox field.
									$build .= '<input name="miu-criteria-settings[roles][]" type="checkbox" id="' . esc_attr( $set_field_id ) . '" value="' . esc_attr( $role_type ) . '">' . esc_html( $role_label );

								// Close the label.
								$build .= '</label>';

							// And close the span.
							$build .= '</span>';
						}

					// Close the fieldset and block.
					$build .= '</fieldset>';
				$build .= '</td>';

			// Close up the row.
			$build .= '</tr>';

		// Close up the table.
		$build .= '</tbody></table>';

	// Close out my div.
	$build .= '</div>';

	// Return if requested.
	if ( ! $echo ) {
		return $build;
	}

	// Echo it out.
	echo $build;
}

/**
 * Handle rendering the submit button along with nonces.
 *
 * @param  boolean $echo  Whether to echo or return them.
 *
 * @return HTML
 */
function display_user_criteria_submit_fields( $echo = true ) {

	// Set an empty.
	$build  = '';

	// Start with a div.
	$build .= '<div class="miu-admin-settings-section-wrap miu-admin-settings-submit-fields-wrap">';

		// Render the hidden nonce field.
		$build .= wp_nonce_field( Core\NONCE_PREFIX . 'criteria_submit', Core\NONCE_PREFIX . 'criteria_set', true, false );

		// Handle our submit button.
		$build .= '<button type="submit" class="miu-admin-settings-button button button-primary" name="miu-admin-criteria-submit" value="go">' . esc_html__( 'Search Users', 'manage-inactive-users' ) . '</button>';

	// Close out my div.
	$build .= '</div>';

	// Return if requested.
	if ( ! $echo ) {
		return $build;
	}

	// Echo it out.
	echo $build;
}

/**
 * Set up the options for displaying the list of pending users.
 *
 * @param  array   $pending_data  The data related to the pending user changes.
 * @param  boolean $echo          Whether to echo or just return it.
 *
 * @return HTML
 */
function display_pending_users_list_fields( $pending_data = array(), $echo = true ) {

	// Bail without data.
	if ( empty( $pending_data ) ) {
		return;
	}

	// preprint( $pending_data, true );

	// Now set my args for the author list itself.
	$set_user_list_args = array(
		'optioncount' => 1,
		'include'     => $pending_data['users'],
		'echo'        => false,
	);

	// Get my raw content.
	$get_user_list_raw  = wp_list_authors( $set_user_list_args );

	// Set an empty.
	$build  = '';

	// Start with a div.
	$build .= '<div class="miu-admin-settings-section-wrap miu-admin-settings-pending-users-wrap">';

		// List the count and timestamp that was used.
		$build .= '<p class="miu-admin-settings-pending-users-intro">' . sprintf( __( 'You are about to change %d users to Subscriber status who have not published content since %s.', 'manage-inactive-users' ), absint( $pending_data['count'] ), date( get_option( 'date_format' ), $pending_data['stamp'] ) ) . '</p>';

		// And the list itself without linking each one.
		$build .= '<div class="miu-admin-settings-pending-users-block">';
			$build .= '<ul class="miu-admin-settings-pending-users-list">';
				$build .= strip_tags( $get_user_list_raw, '<li>' );
			$build .= '</ul>';
		$build .= '</div>';

	// Close out my div.
	$build .= '</div>';

	// Return if requested.
	if ( ! $echo ) {
		return $build;
	}

	// Echo it out.
	echo $build;
}

/**
 * Handle rendering the submit button along with nonces.
 *
 * @param  boolean $echo  Whether to echo or return them.
 *
 * @return HTML
 */
function display_pending_users_submit_fields( $echo = true ) {

	// Set an empty.
	$build  = '';

	// Start with a div.
	$build .= '<div class="miu-admin-settings-section-wrap miu-admin-settings-submit-fields-wrap">';

		// Render the hidden nonce field.
		$build .= wp_nonce_field( Core\NONCE_PREFIX . 'pending_submit', Core\NONCE_PREFIX . 'pending_set', true, false );

		// Handle our submit button.
		$build .= '<button type="submit" class="miu-admin-settings-button button button-primary" name="miu-admin-pending-submit" value="go">' . esc_html__( 'Update Users', 'manage-inactive-users' ) . '</button>';

		// And our clear / delete.
		$build .= '<button type="submit" class="miu-admin-settings-button miu-admin-settings-button-alt button button-secondary" name="miu-admin-pending-clear" value="go">' . esc_html__( 'Clear Pending Data', 'manage-inactive-users' ) . '</button>';

	// Close out my div.
	$build .= '</div>';

	// Return if requested.
	if ( ! $echo ) {
		return $build;
	}

	// Echo it out.
	echo $build;
}

/**
 * Build the markup for an admin notice.
 *
 * @param  string  $notice       The actual message to display.
 * @param  string  $result       Which type of message it is.
 * @param  boolean $dismiss      Whether it should be dismissable.
 * @param  boolean $show_button  Show the dismiss button (for Ajax calls).
 * @param  boolean $echo         Whether to echo out the markup or return it.
 *
 * @return HTML
 */
function display_admin_notice_markup( $notice = '', $result = 'error', $dismiss = true, $show_button = false, $echo = true ) {

	// Bail without the required message text.
	if ( empty( $notice ) ) {
		return;
	}

	// Set my base class.
	$class  = 'notice notice-' . esc_attr( $result ) . ' miu-admin-notice-message';

	// Add the dismiss class.
	if ( $dismiss ) {
		$class .= ' is-dismissible';
	}

	// Set an empty.
	$build  = '';

	// Start the notice markup.
	$build .= '<div class="' . esc_attr( $class ) . '">';

		// Display the actual message.
		$build .= '<p><strong>' . wp_kses_post( $notice ) . '</strong></p>';

		// Show the button if we set dismiss and button variables.
		$build .= $dismiss && $show_button ? '<button type="button" class="notice-dismiss">' . screen_reader_text() . '</button>' : '';

	// And close the div.
	$build .= '</div>';

	// Echo it if requested.
	if ( ! empty( $echo ) ) {
		echo $build; // WPCS: XSS ok.
	}

	// Just return it.
	return $build;
}
