<?php
/**
 * Plugin Name: Manage Inactive Users
 * Plugin URI:  https://github.com/norcross/manage-inactive-users
 * Description: Set inactive users down to subscriber status based on latest published post.
 * Version:     0.0.1
 * Author:      Andrew Norcross
 * Author URI:  https://github.com/norcross
 * Text Domain: manage-inactive-users
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package ManageInactiveUsers
 */

// Declare our namespace.
namespace NorcrossPlugins\ManageInactiveUsers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Define our plugin version.
define( __NAMESPACE__ . '\VERS', '0.0.1' );

// Plugin root file.
define( __NAMESPACE__ . '\FILE', __FILE__ );

// Define our file base.
define( __NAMESPACE__ . '\BASE', plugin_basename( __FILE__ ) );

// Plugin Folder URL.
define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

// Set our assets URL constant.
define( __NAMESPACE__ . '\ASSETS_URL', URL . 'assets' );

// Set our includes and template path constants.
define( __NAMESPACE__ . '\INCLUDES_PATH', __DIR__ . '/includes' );

// Set the various prefixes for our actions and filters.
define( __NAMESPACE__ . '\HOOK_PREFIX', 'wp_miu_' );
define( __NAMESPACE__ . '\NONCE_PREFIX', 'wp_miu_nonce_' );
define( __NAMESPACE__ . '\TRANSIENT_PREFIX', 'wpmiu_tr_' );
define( __NAMESPACE__ . '\OPTION_PREFIX', 'wp_miu_setting_' );

// Set our menu root.
define( __NAMESPACE__ . '\MENU_ROOT', 'manage-inactive-users' );

// Now we handle all the various file loading.
norcrossplugins_wp_miu_file_load();

/**
 * Actually load our files.
 *
 * @return void
 */
function norcrossplugins_wp_miu_file_load() {

	// Load the multi-use files first.
	require_once __DIR__ . '/includes/utilities.php';
	require_once __DIR__ . '/includes/helpers.php';

	// Handle our admin items.
	require_once __DIR__ . '/includes/admin/setup.php';
	require_once __DIR__ . '/includes/admin/markup.php';
	require_once __DIR__ . '/includes/admin/menu-items.php';
	require_once __DIR__ . '/includes/admin/notices.php';
	require_once __DIR__ . '/includes/admin/process.php';

	// Load the triggered file loads.
	require_once __DIR__ . '/includes/activate.php';
	require_once __DIR__ . '/includes/deactivate.php';
	require_once __DIR__ . '/includes/uninstall.php';
}
