<?php
/**
 * Our deactivation call.
 *
 * @package ManageInactiveUsers
 */

// Declare our namespace.
namespace NorcrossPlugins\ManageInactiveUsers\Deactivate;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;
use NorcrossPlugins\ManageInactiveUsers\Helpers as Helpers;

/**
 * Delete various options when deactivating the plugin.
 *
 * @return void
 */
function deactivate() {

	// Delete the data.
	Helpers\clear_pending_data();

	// Include our action so that we may add to this later.
	do_action( Core\HOOK_PREFIX . 'deactivate_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( Core\FILE, __NAMESPACE__ . '\deactivate' );
