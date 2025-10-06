<?php
/**
 * Plugin Name: MainWP CribOps Extension
 * Plugin URI: https://github.com/CloudBedrock/mainwp-cribops-extension
 * Description: MainWP Extension to control and manage CribOps WP Kit across multiple WordPress sites
 * Version: 1.3.1
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
define('MAINWP_CRIBOPS_VERSION', '1.3.1');
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
    $check_link = '<a href="#" onclick="jQuery.post(ajaxurl, {action: \'mainwp_cribops_check_update\'}, function(response) { if(response.success && response.data.update) { alert(\'Update available: v\' + response.data.version); window.location.href = \'' . admin_url('plugins.php') . '\'; } else { alert(\'No updates available. Current version: ' . MAINWP_CRIBOPS_VERSION . '\'); } }); return false;">Check for updates</a>';
    array_unshift($links, $check_link);
    return $links;
});

// Add AJAX handler for checking updates
add_action('wp_ajax_mainwp_cribops_check_update', function() {
    // Force transient deletion to check for updates
    delete_transient('mainwp_cribops_github_release');
    delete_site_transient('update_plugins');

    // Get the updater instance and check for updates
    $updater = new MainWP_CribOps_GitHub_Updater(MAINWP_CRIBOPS_PLUGIN_FILE);
    $github_data = $updater->get_latest_release();

    if ($github_data && version_compare($github_data->tag_name, 'v' . MAINWP_CRIBOPS_VERSION, '>')) {
        // Force WordPress to check for updates
        wp_update_plugins();

        wp_send_json_success(array(
            'update' => true,
            'version' => str_replace('v', '', $github_data->tag_name),
            'download_url' => $github_data->zipball_url,
            'details_url' => $github_data->html_url
        ));
    } else {
        wp_send_json_success(array(
            'update' => false,
            'current_version' => MAINWP_CRIBOPS_VERSION
        ));
    }
});