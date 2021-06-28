<?php
/**
 * Our uninstall call.
 *
 * @package ManageInactiveUsers
 */

// Declare our namespace.
namespace NorcrossPlugins\ManageInactiveUsers\Uninstall;

// Set our aliases.
use NorcrossPlugins\ManageInactiveUsers as Core;
use NorcrossPlugins\ManageInactiveUsers\Helpers as Helpers;

/**
 * Delete various options when uninstalling the plugin.
 *
 * @return void
 */
function uninstall() {

	// Delete the data.
	Helpers\clear_pending_data();

	// Include our action so that we may add to this later.
	do_action( Core\HOOK_PREFIX . 'uninstall_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_uninstall_hook( Core\FILE, __NAMESPACE__ . '\uninstall' );
