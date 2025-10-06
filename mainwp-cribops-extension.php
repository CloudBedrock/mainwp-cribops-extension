<?php
/**
 * Plugin Name: MainWP CribOps Extension
 * Plugin URI: https://github.com/CloudBedrock/mainwp-cribops-extension
 * Description: MainWP Extension to control and manage CribOps WP Kit across multiple WordPress sites
 * Version: 1.0.1
 * Author: CribOps Development Team
 * Author URI: https://cribops.com
 * Text Domain: mainwp-cribops
 * Requires at least: 5.0
 * Tested up to: 6.7.1
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MAINWP_CRIBOPS_VERSION', '1.0.1');
define('MAINWP_CRIBOPS_PLUGIN_FILE', __FILE__);
define('MAINWP_CRIBOPS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MAINWP_CRIBOPS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load the GitHub updater
require_once MAINWP_CRIBOPS_PLUGIN_DIR . 'class-mainwp-cribops-github-updater.php';

// Load the main extension activator class
require_once MAINWP_CRIBOPS_PLUGIN_DIR . 'class-mainwp-cribops-extension-activator.php';

// Initialize the GitHub updater
function mainwp_cribops_init_updater() {
    new MainWP_CribOps_GitHub_Updater(__FILE__);
}
add_action('plugins_loaded', 'mainwp_cribops_init_updater');

// Add manual check for updates link
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $check_link = '<a href="' . wp_nonce_url(admin_url('update-core.php?force-check=1'), 'force-check') . '">Check for updates</a>';
    array_unshift($links, $check_link);
    return $links;
});