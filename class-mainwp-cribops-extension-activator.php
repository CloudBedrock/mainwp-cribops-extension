<?php

class MainWP_CribOps_Extension_Activator {

    protected $mainwpMainActivated = false;
    protected $childEnabled = false;
    protected $childKey = false;
    protected $childFile;
    protected $plugin_handle = 'mainwp-cribops-extension';
    protected $product_id = 'MainWP CribOps Extension';
    protected $software_version = '1.4.6';

    public function __construct() {
        $this->childFile = MAINWP_CRIBOPS_PLUGIN_FILE;

        add_filter('mainwp_getextensions', array($this, 'get_this_extension'));
        $this->mainwpMainActivated = apply_filters('mainwp_activated_check', false);

        if ($this->mainwpMainActivated !== false) {
            $this->activate_this_plugin();
        } else {
            add_action('mainwp_activated', array($this, 'activate_this_plugin'));
        }

        add_action('admin_init', array($this, 'admin_init'));
    }

    public function admin_init() {
        // Remove this - let MainWP handle the page rendering
    }

    public function get_this_extension($pArray) {
        $pArray[] = array(
            'plugin' => MAINWP_CRIBOPS_PLUGIN_FILE,
            'api' => $this->plugin_handle,
            'mainwp' => true,
            'callback' => array($this, 'settings'),
            'apiManager' => false
        );
        return $pArray;
    }

    public function settings() {
        do_action('mainwp_pageheader_extensions', MAINWP_CRIBOPS_PLUGIN_FILE);
        $this->render_extension_page();
        do_action('mainwp_pagefooter_extensions', MAINWP_CRIBOPS_PLUGIN_FILE);
    }

    public function activate_this_plugin() {
        $this->mainwpMainActivated = apply_filters('mainwp_activated_check', $this->mainwpMainActivated);
        $this->childEnabled = apply_filters('mainwp_extension_enabled_check', __FILE__);
        $this->childKey = $this->childEnabled['key'];

        if (function_exists('mainwp_current_user_have_right')) {
            if (!mainwp_current_user_have_right('extension', 'mainwp-cribops-extension')) {
                return;
            }
        }

        add_action('wp_ajax_mainwp_cribops_get_sites', array($this, 'ajax_get_sites'));
        add_action('wp_ajax_mainwp_cribops_run_action', array($this, 'ajax_run_action'));
        add_action('wp_ajax_mainwp_cribops_manage_site', array($this, 'ajax_manage_site'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add site-specific management pages
        add_filter('mainwp_getsubpages_sites', array($this, 'add_site_page'), 10, 1);
    }

    public function enqueue_scripts() {
        if (isset($_GET['page']) && $_GET['page'] == 'Extensions-Mainwp-Cribops-Extension') {
            wp_enqueue_script('mainwp-cribops-js', plugins_url('assets/admin.js', dirname(__FILE__)), array('jquery'));
            wp_enqueue_style('mainwp-cribops-css', plugins_url('assets/admin.css', dirname(__FILE__)));
            wp_localize_script('mainwp-cribops-js', 'mainwpCribOps', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mainwp-cribops-nonce')
            ));
        }
    }

    public function render_extension_page() {
        // Ensure scripts are loaded
        wp_enqueue_script('jquery');
        wp_enqueue_script('mainwp-cribops-js', MAINWP_CRIBOPS_PLUGIN_URL . 'assets/admin.js', array('jquery'), MAINWP_CRIBOPS_VERSION, true);
        wp_enqueue_style('mainwp-cribops-css', MAINWP_CRIBOPS_PLUGIN_URL . 'assets/admin.css', array(), MAINWP_CRIBOPS_VERSION);
        wp_localize_script('mainwp-cribops-js', 'mainwpCribOps', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mainwp-cribops-nonce')
        ));
        ?>
        <div class="wrap">
            <h1>MainWP CribOps WP Kit Control</h1>

            <div class="mainwp-cribops-container">
                <div class="notice notice-info">
                    <p>Manage CribOps WP Kit installations across your network of WordPress sites.</p>
                </div>

                <div class="card">
                    <h2>Site Status</h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Site</th>
                                <th>CribOps WP Kit Status</th>
                                <th>Version</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cribops-sites-list">
                            <tr>
                                <td colspan="4">Loading sites...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <h2>Bulk Actions</h2>
                    <div class="cribops-bulk-actions">
                        <button class="button button-primary" id="cribops-sync-all">
                            Sync All Sites
                        </button>
                        <button class="button" id="cribops-install-plugins">
                            Trigger Plugin Installation
                        </button>
                        <button class="button" id="cribops-manage-licenses">
                            Manage Licenses
                        </button>
                    </div>
                </div>

                <div class="card">
                    <h2>Available Functions</h2>
                    <ul>
                        <li><strong>Get Status:</strong> Check CribOps WP Kit installation status</li>
                        <li><strong>Install Plugins:</strong> Trigger bulk plugin installation recipes</li>
                        <li><strong>Manage Licenses:</strong> Push license keys to child sites</li>
                        <li><strong>Sync Settings:</strong> Synchronize CribOps WP Kit settings</li>
                        <li><strong>View Logs:</strong> Check installation and error logs</li>
                    </ul>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        var mainwpCribOps = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
        };

        jQuery(document).ready(function($) {
            console.log('CribOps Extension Loaded');

            // Load sites immediately
            loadCribOpsSites();

            function loadCribOpsSites() {
                console.log('Loading sites...');
                $('#cribops-sites-list').html('<tr><td colspan="4">Loading sites...</td></tr>');

                $.post(mainwpCribOps.ajaxurl, {
                    action: 'mainwp_cribops_get_sites',
                    nonce: mainwpCribOps.nonce
                }, function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        displaySites(response.data);
                    } else {
                        $('#cribops-sites-list').html('<tr><td colspan="4">Error loading sites: ' + (response.data ? response.data.error : 'Unknown error') + '</td></tr>');
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    $('#cribops-sites-list').html('<tr><td colspan="4">AJAX Error: ' + textStatus + '</td></tr>');
                });
            }

            function displaySites(sites) {
                var html = '';

                if (!sites || sites.length === 0) {
                    html = '<tr><td colspan="4">No sites found</td></tr>';
                } else {
                    sites.forEach(function(site) {
                        var status = site.cribops_status.installed ?
                            '<span style="color: green;">Installed</span>' :
                            '<span style="color: red;">Not Installed</span>';

                        var active = site.cribops_status.active ?
                            '<span style="color: green;">Active</span>' :
                            '<span style="color: gray;">Inactive</span>';

                        html += '<tr>';
                        html += '<td><a href="' + site.url + '" target="_blank">' + site.name + '</a></td>';
                        html += '<td>' + status + ' / ' + active + '</td>';
                        html += '<td>' + site.cribops_status.version + '</td>';
                        html += '<td>';
                        html += '<button class="button button-small cribops-site-action" data-site-id="' + site.id + '" data-action="sync">Sync</button> ';
                        html += '<a href="admin.php?page=managesites&id=' + site.id + '&tab=cribops-wp-kit" class="button button-primary button-small">Manage</a>';
                        html += '</td>';
                        html += '</tr>';
                    });
                }

                $('#cribops-sites-list').html(html);
            }

            // Button handlers
            $('#cribops-sync-all').on('click', function() {
                alert('Sync all functionality coming soon');
            });

            // Individual site action buttons
            $(document).on('click', '.cribops-site-action', function() {
                var $button = $(this);
                var siteId = $button.data('site-id');
                var action = $button.data('action');

                $button.prop('disabled', true).text('Processing...');

                console.log('Running action:', action, 'for site:', siteId);

                $.post(mainwpCribOps.ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: action,
                    nonce: mainwpCribOps.nonce
                }, function(response) {
                    console.log('Action response:', response);
                    if (response.success) {
                        if (action === 'sync') {
                            alert('Sync completed! Refreshing site list...');
                            loadCribOpsSites();
                        } else {
                            alert('Action completed successfully');
                        }
                    } else {
                        alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                    }

                    // Reset button text
                    if (action === 'sync') {
                        $button.text('Sync');
                    } else if (action === 'install') {
                        $button.text('Install Plugins');
                    } else if (action === 'settings') {
                        $button.text('Settings');
                    }
                    $button.prop('disabled', false);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    alert('Communication error: ' + textStatus);
                    $button.prop('disabled', false).text('Retry');
                });
            });
        });
        </script>
        <?php
    }

    public function ajax_get_sites() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mainwp-cribops-nonce')) {
            wp_send_json_error(array('error' => 'Security check failed'));
            return;
        }

        try {
            // Get all MainWP sites using the database directly
            if (class_exists('MainWP\Dashboard\MainWP_DB')) {
                $websites = \MainWP\Dashboard\MainWP_DB::instance()->get_websites_by_user_id(1); // Admin user
                $sites = array();

                if ($websites) {
                    foreach ($websites as $website) {
                        $cribops_data = get_option('mainwp_cribops_site_' . $website->id, array());

                        $sites[] = array(
                            'id' => $website->id,
                            'name' => $website->name,
                            'url' => $website->url,
                            'cribops_status' => array(
                                'installed' => isset($cribops_data['cribops_installed']) ? $cribops_data['cribops_installed'] : false,
                                'version' => isset($cribops_data['cribops_version']) ? $cribops_data['cribops_version'] : 'Unknown',
                                'active' => isset($cribops_data['cribops_active']) ? $cribops_data['cribops_active'] : false
                            )
                        );
                    }
                }

                wp_send_json_success($sites);
            } else {
                // Fallback to filters method
                global $mainWPCribOpsExtensionActivator;
                $websites = apply_filters('mainwp_getsites', $mainWPCribOpsExtensionActivator->childFile, $mainWPCribOpsExtensionActivator->childKey);
                $sites = array();

                if (is_array($websites)) {
                    foreach ($websites as $website) {
                        $cribops_data = get_option('mainwp_cribops_site_' . (isset($website['id']) ? $website['id'] : 0), array());

                        $sites[] = array(
                            'id' => isset($website['id']) ? $website['id'] : 0,
                            'name' => isset($website['name']) ? $website['name'] : 'Unknown',
                            'url' => isset($website['url']) ? $website['url'] : '#',
                            'cribops_status' => array(
                                'installed' => isset($cribops_data['cribops_installed']) ? $cribops_data['cribops_installed'] : false,
                                'version' => isset($cribops_data['cribops_version']) ? $cribops_data['cribops_version'] : 'Unknown',
                                'active' => isset($cribops_data['cribops_active']) ? $cribops_data['cribops_active'] : false
                            )
                        );
                    }
                }

                wp_send_json_success($sites);
            }
        } catch (Exception $e) {
            wp_send_json_error(array('error' => 'Exception: ' . $e->getMessage()));
        }
    }

    /**
     * Add CribOps WP Kit tab to individual site pages
     */
    public function add_site_page($subPages) {
        $subPages[] = array(
            'title' => 'CribOps WP Kit',
            'slug' => 'cribops-wp-kit',
            'sitetab' => true,
            'callback' => array($this, 'render_site_page'),
        );
        return $subPages;
    }

    /**
     * Render the CribOps management page for a specific site
     */
    public function render_site_page() {
        if (!isset($_GET['id'])) {
            echo '<div class="notice notice-error"><p>No site ID provided.</p></div>';
            return;
        }

        $site_id = intval($_GET['id']);

        // Load the UI class if not already loaded
        if (!class_exists('MainWP_CribOps_UI')) {
            require_once MAINWP_CRIBOPS_PLUGIN_DIR . 'includes/class-mainwp-cribops-ui.php';
        }

        // Render the site management interface
        MainWP_CribOps_UI::render_site_management($site_id);
    }

    /**
     * AJAX handler for managing a specific site
     */
    public function ajax_manage_site() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mainwp-cribops-nonce')) {
            wp_send_json_error(array('error' => 'Security check failed'));
            return;
        }

        $site_id = isset($_POST['site_id']) ? intval($_POST['site_id']) : 0;

        if (!$site_id) {
            wp_send_json_error(array('error' => 'No site ID provided'));
            return;
        }

        // Return the site management URL
        $url = admin_url('admin.php?page=managesites&id=' . $site_id . '&tab=cribops-wp-kit');
        wp_send_json_success(array('url' => $url));
    }

    public function ajax_run_action() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mainwp-cribops-nonce')) {
            wp_send_json_error(array('error' => 'Security check failed'));
            return;
        }

        $site_id = isset($_POST['site_id']) ? intval($_POST['site_id']) : 0;
        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        $args = isset($_POST['args']) ? $_POST['args'] : array();

        if (!$site_id || !$action) {
            wp_send_json_error(array('error' => 'Missing site_id or action'));
            return;
        }

        try {
            // Get the specific site
            if (class_exists('MainWP\Dashboard\MainWP_DB') && class_exists('MainWP\Dashboard\MainWP_Connect')) {
                $website = \MainWP\Dashboard\MainWP_DB::instance()->get_website_by_id($site_id);

                if (!$website) {
                    wp_send_json_error(array('error' => 'Site not found'));
                    return;
                }

                // Prepare the request
                $information = array(
                    'action' => 'cribops_' . $action,
                    'args' => $args
                );

                // Call the child site
                $result = \MainWP\Dashboard\MainWP_Connect::fetch_url_authed(
                    $website,
                    'extra_execution',
                    $information
                );

                // Store CribOps data if returned
                if (is_array($result)) {
                    // Check if we have cribops_data in the response
                    if (isset($result['cribops_data'])) {
                        // Store complete data including auth configuration
                        update_option('mainwp_cribops_site_' . $site_id, $result['cribops_data']);

                        // Store auth config separately for easy access
                        if (isset($result['cribops_data']['auth_config'])) {
                            update_option('mainwp_cribops_auth_' . $site_id, $result['cribops_data']['auth_config']);
                        }
                    }
                    // Also check if the sync action returned data directly
                    else if ($action === 'sync' && isset($result['status'])) {
                        // Sync action returns data in a different format
                        $cribops_data = isset($result['status']) ? $result['status'] : $result;
                        update_option('mainwp_cribops_site_' . $site_id, $cribops_data);
                    }
                }

                wp_send_json_success($result);
            } else {
                // Fallback method
                global $mainWPCribOpsExtensionActivator;
                $websites = apply_filters('mainwp_getsites', $mainWPCribOpsExtensionActivator->childFile, $mainWPCribOpsExtensionActivator->childKey, $site_id);

                if (empty($websites)) {
                    wp_send_json_error(array('error' => 'Site not found'));
                    return;
                }

                $website = $websites[0];

                $information = array(
                    'action' => 'cribops_' . $action
                );

                $result = apply_filters('mainwp_fetchurlauthed', $mainWPCribOpsExtensionActivator->childFile, $mainWPCribOpsExtensionActivator->childKey, $website, 'extra_execution', $information);

                if (is_array($result) && isset($result['cribops_data'])) {
                    update_option('mainwp_cribops_site_' . $site_id, $result['cribops_data']);
                }

                wp_send_json_success($result);
            }
        } catch (Exception $e) {
            wp_send_json_error(array('error' => 'Exception: ' . $e->getMessage()));
        }
    }
}

global $mainWPCribOpsExtensionActivator;
$mainWPCribOpsExtensionActivator = new MainWP_CribOps_Extension_Activator();