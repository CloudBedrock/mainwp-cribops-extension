<?php
/**
 * Plugin Name: MainWP CribOps Extension
 * Plugin URI: https://github.com/CloudBedrock/mainwp-cribops-extension
 * Description: MainWP Extension to control and manage CribOps WP Kit across multiple WordPress sites
 * Version: 1.0.0
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
define('MAINWP_CRIBOPS_VERSION', '1.0.0');
define('MAINWP_CRIBOPS_PLUGIN_FILE', __FILE__);
define('MAINWP_CRIBOPS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MAINWP_CRIBOPS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load the main extension class
require_once MAINWP_CRIBOPS_PLUGIN_DIR . 'class-mainwp-cribops-extension.php';

// Initialize the extension
function mainwp_cribops_extension_init() {
    new MainWP_CribOps_Extension();
}

// Hook into MainWP initialization
add_action('init', 'mainwp_cribops_extension_init');