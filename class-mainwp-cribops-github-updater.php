<?php
/**
 * MainWP CribOps Extension GitHub Updater
 *
 * Handles automatic updates from GitHub releases
 */

if (!defined('ABSPATH')) {
    exit;
}

class MainWP_CribOps_GitHub_Updater {

    private $github_user = 'CloudBedrock';
    private $github_repo = 'mainwp-cribops-extension';
    private $plugin_file;
    private $plugin_data;
    private $plugin_slug;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($this->plugin_file);

        add_action('init', array($this, 'init'));
    }

    public function init() {
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_source_selection', array($this, 'fix_plugin_folder'), 10, 4);
    }

    private function get_plugin_data() {
        if ($this->plugin_data === null) {
            $this->plugin_data = get_plugin_data($this->plugin_file);
        }
        return $this->plugin_data;
    }

    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $plugin_data = $this->get_plugin_data();
        $current_version = $plugin_data['Version'];

        // Get latest release from GitHub
        $release_info = $this->get_latest_release();

        if ($release_info && version_compare($release_info->tag_name, 'v' . $current_version, '>')) {
            $update = new stdClass();
            $update->id = $this->plugin_slug;
            $update->slug = dirname($this->plugin_slug);
            $update->plugin = $this->plugin_slug;
            $update->new_version = str_replace('v', '', $release_info->tag_name);
            $update->url = 'https://github.com/' . $this->github_user . '/' . $this->github_repo;
            $update->package = $this->get_download_url($release_info);
            $update->icons = array(
                'default' => 'https://raw.githubusercontent.com/' . $this->github_user . '/' . $this->github_repo . '/main/assets/icon-128x128.png'
            );
            $update->tested = get_bloginfo('version');
            $update->requires = '5.0';

            $transient->response[$this->plugin_slug] = $update;
        }

        return $transient;
    }

    public function plugin_info($false, $action, $args) {
        if ($action !== 'plugin_information') {
            return false;
        }

        if (!isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
            return false;
        }

        $release_info = $this->get_latest_release();

        if (!$release_info) {
            return false;
        }

        $plugin_data = $this->get_plugin_data();

        $info = new stdClass();
        $info->name = $plugin_data['Name'];
        $info->slug = dirname($this->plugin_slug);
        $info->version = str_replace('v', '', $release_info->tag_name);
        $info->author = $plugin_data['Author'];
        $info->homepage = $plugin_data['PluginURI'];
        $info->download_link = $this->get_download_url($release_info);
        $info->sections = array(
            'description' => $plugin_data['Description'],
            'changelog' => $this->parse_changelog($release_info->body)
        );
        $info->banners = array();
        $info->icons = array(
            'default' => 'https://raw.githubusercontent.com/' . $this->github_user . '/' . $this->github_repo . '/main/assets/icon-128x128.png'
        );

        return $info;
    }

    public function fix_plugin_folder($source, $remote_source, $upgrader, $hook_extra) {
        if (isset($hook_extra['plugin']) && $hook_extra['plugin'] === $this->plugin_slug) {
            $corrected_source = trailingslashit($remote_source) . dirname($this->plugin_slug) . '/';

            if (rename($source, $corrected_source)) {
                return $corrected_source;
            } else {
                return new WP_Error('rename_failed', 'Unable to rename the plugin folder.');
            }
        }

        return $source;
    }

    private function get_latest_release() {
        $cache_key = 'mainwp_cribops_github_release';
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $api_url = 'https://api.github.com/repos/' . $this->github_user . '/' . $this->github_repo . '/releases/latest';

        $response = wp_remote_get($api_url, array(
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'MainWP-CribOps-Extension'
            ),
            'timeout' => 10
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $release = json_decode(wp_remote_retrieve_body($response));

        if ($release) {
            set_transient($cache_key, $release, HOUR_IN_SECONDS * 6);
        }

        return $release;
    }

    private function get_download_url($release_info) {
        if (!empty($release_info->assets)) {
            foreach ($release_info->assets as $asset) {
                if (strpos($asset->name, '.zip') !== false && strpos($asset->name, 'mainwp-cribops-extension') !== false) {
                    return $asset->browser_download_url;
                }
            }
        }

        // Fallback to zipball if no asset found
        return $release_info->zipball_url;
    }

    private function parse_changelog($body) {
        $changelog = '<h4>Changes in this version:</h4>';
        $changelog .= nl2br(esc_html($body));
        return $changelog;
    }

    /**
     * Manual update check
     */
    public function force_check() {
        delete_transient('mainwp_cribops_github_release');
        wp_update_plugins();
    }
}