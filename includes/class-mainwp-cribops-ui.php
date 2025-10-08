<?php
/**
 * MainWP CribOps Extension UI Class
 *
 * Handles the enhanced UI for managing plugins and themes
 */

class MainWP_CribOps_UI {

    /**
     * Render the site management interface
     */
    public static function render_site_management($site_id) {
        $site = self::get_site_info($site_id);
        if (!$site) {
            echo '<div class="notice notice-error"><p>Site not found.</p></div>';
            return;
        }

        // Add MainWP page header
        do_action('mainwp_pageheader_sites', 'cribops-wp-kit');
        ?>
        <div class="wrap mainwp-cribops-site-management">
            <h2>Manage CribOps WP Kit - <?php echo esc_html($site->name); ?></h2>

            <div class="cribops-nav-wrapper">
                <button class="cribops-nav-btn cribops-nav-active" data-tab="plugins">Installed Plugins</button>
                <button class="cribops-nav-btn" data-tab="themes">Installed Themes</button>
                <button class="cribops-nav-btn" data-tab="available-plugins">Available Plugins</button>
                <button class="cribops-nav-btn" data-tab="available-themes">Available Themes</button>
                <button class="cribops-nav-btn" data-tab="packages">Packages</button>
                <button class="cribops-nav-btn" data-tab="settings">Settings</button>
                <button class="cribops-nav-btn" data-tab="logs">Activity Logs</button>
            </div>

            <div id="plugins" class="tab-content active">
                <h3>Installed Plugins</h3>
                <div class="cribops-toolbar">
                    <button class="button" id="refresh-plugins">Refresh List</button>
                    <button class="button button-primary" id="bulk-activate">Bulk Activate</button>
                    <button class="button" id="bulk-deactivate">Bulk Deactivate</button>
                    <button class="button button-link-delete" id="bulk-delete">Bulk Delete</button>
                </div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all-plugins">
                            </td>
                            <th>Plugin</th>
                            <th>Version</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="installed-plugins-list">
                        <tr><td colspan="5">Loading plugins...</td></tr>
                    </tbody>
                </table>
            </div>

            <div id="themes" class="tab-content">
                <h3>Installed Themes</h3>
                <div class="cribops-toolbar">
                    <button class="button" id="refresh-themes">Refresh List</button>
                </div>
                <div class="theme-browser" id="installed-themes-list">
                    <div class="themes-loading">Loading themes...</div>
                </div>
            </div>

            <div id="available-plugins" class="tab-content">
                <h3>Available Plugins from Site's Repository</h3>
                <div class="notice notice-info">
                    <p id="repo-info">Loading repository information...</p>
                </div>
                <div class="cribops-toolbar">
                    <input type="text" id="search-available-plugins" class="regular-text" placeholder="Search plugins by name...">
                    <button class="button" id="refresh-available-plugins">Refresh Available</button>
                </div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all-available-plugins">
                            </td>
                            <th>Plugin</th>
                            <th>Version</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="available-plugins-list">
                        <tr><td colspan="5">Loading available plugins from site's repository...</td></tr>
                    </tbody>
                </table>
            </div>

            <div id="available-themes" class="tab-content">
                <h3>Available Themes from Site's Repository</h3>
                <div class="cribops-toolbar">
                    <input type="text" id="search-available-themes" class="regular-text" placeholder="Search themes by name...">
                    <button class="button" id="refresh-available-themes">Refresh Available</button>
                </div>
                <div class="theme-browser" id="available-themes-list">
                    <div class="themes-loading">Loading available themes...</div>
                </div>
            </div>

            <div id="packages" class="tab-content">
                <h3>Prime Mover Packages</h3>
                <div class="cribops-toolbar">
                    <input type="text" id="search-packages" class="regular-text" placeholder="Search packages by name...">
                    <button class="button" id="refresh-packages">Refresh Packages</button>
                </div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Description</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="packages-list">
                        <tr><td colspan="4">Loading packages from site's repository...</td></tr>
                    </tbody>
                </table>
            </div>

            <div id="settings" class="tab-content">
                <h3>Site's CribOps WP Kit Configuration</h3>
                <div class="notice notice-info">
                    <p>These settings are configured on the child site. To change them, update the site's configuration directly.</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th>Authentication Type</th>
                        <td>
                            <span id="site-auth-type" class="description">Loading...</span>
                        </td>
                    </tr>
                    <tr>
                        <th>API Endpoint</th>
                        <td>
                            <span id="site-api-endpoint" class="description">Loading...</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Repository Status</th>
                        <td>
                            <span id="site-repo-status" class="description">Loading...</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Bearer Token</th>
                        <td>
                            <span id="site-bearer-status" class="description">Loading...</span>
                        </td>
                    </tr>
                </table>
                <p>
                    <button type="button" class="button" id="refresh-settings">Refresh Settings</button>
                    <button type="button" class="button" id="sync-site">Sync with Site</button>
                </p>
            </div>

            <div id="logs" class="tab-content">
                <h3>Activity Logs</h3>
                <div class="cribops-toolbar">
                    <button class="button" id="refresh-logs">Refresh Logs</button>
                    <button class="button" id="clear-logs">Clear Logs</button>
                </div>
                <div id="activity-logs-list">
                    <p>Loading activity logs...</p>
                </div>
            </div>
        </div>

        <style>
            /* Navigation Buttons - WP Rocket Style */
            .cribops-nav-wrapper {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin: 20px 0 30px;
                padding: 0;
            }
            .cribops-nav-btn {
                background: #2c3338;
                color: #fff;
                border: none;
                padding: 10px 20px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                border-radius: 4px;
                transition: all 0.2s ease;
                outline: none;
            }
            .cribops-nav-btn:hover {
                background: #1d2327;
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .cribops-nav-btn.cribops-nav-active {
                background: #0073aa;
                box-shadow: 0 2px 6px rgba(0,115,170,0.3);
            }
            .cribops-nav-btn.cribops-nav-active:hover {
                background: #005a87;
            }

            /* Tab Content */
            .mainwp-cribops-site-management .tab-content {
                display: none;
                padding: 20px 0;
            }
            .mainwp-cribops-site-management .tab-content.active {
                display: block;
            }

            /* Toolbar */
            .cribops-toolbar {
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .cribops-toolbar input[type="text"] {
                min-width: 300px;
            }
            .cribops-toolbar button,
            .cribops-toolbar select {
                margin-right: 10px;
            }

            /* Theme Browser */
            .theme-browser {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }
            .theme-item {
                border: 1px solid #ddd;
                padding: 10px;
                width: 300px;
                border-radius: 4px;
                background: #fff;
            }
            .theme-item.active {
                border-color: #0073aa;
            }
            .theme-screenshot {
                width: 100%;
                height: auto;
                border-radius: 3px;
            }

            /* Activity Logs */
            #activity-logs-list {
                max-height: 500px;
                overflow-y: auto;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 4px;
                background: #fff;
            }
            .log-entry {
                padding: 5px 0;
                border-bottom: 1px solid #eee;
            }
            .log-entry:last-child {
                border-bottom: none;
            }

            /* Responsive */
            @media screen and (max-width: 782px) {
                .cribops-nav-wrapper {
                    flex-direction: column;
                }
                .cribops-nav-btn {
                    width: 100%;
                }
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            var siteId = <?php echo intval($site_id); ?>;

            // Store data globally for filtering
            var availablePluginsData = [];
            var availableThemesData = [];
            var packagesData = [];

            // Tab switching
            $('.cribops-nav-btn').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');

                $('.cribops-nav-btn').removeClass('cribops-nav-active');
                $(this).addClass('cribops-nav-active');

                $('.tab-content').removeClass('active');
                $('#' + tab).addClass('active');

                // Load data for the tab if needed
                if (tab === 'plugins' && $('#installed-plugins-list tr').length === 1) {
                    loadInstalledPlugins();
                } else if (tab === 'themes' && $('#installed-themes-list .themes-loading').length) {
                    loadInstalledThemes();
                } else if (tab === 'available-plugins' && $('#available-plugins-list tr').length === 1) {
                    loadAvailablePlugins();
                } else if (tab === 'available-themes' && $('#available-themes-list .themes-loading').length) {
                    loadAvailableThemes();
                } else if (tab === 'packages' && $('#packages-list tr').length === 1) {
                    loadPackages();
                } else if (tab === 'settings') {
                    loadSettings();
                } else if (tab === 'logs') {
                    loadActivityLogs();
                }
            });

            // Load installed plugins on page load
            loadInstalledPlugins();

            function loadInstalledPlugins() {
                $('#installed-plugins-list').html('<tr><td colspan="5">Loading plugins...</td></tr>');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'get_installed_plugins',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success && response.data.plugins) {
                        displayInstalledPlugins(response.data.plugins);
                    } else {
                        $('#installed-plugins-list').html('<tr><td colspan="5">Error loading plugins</td></tr>');
                    }
                });
            }

            function displayInstalledPlugins(plugins) {
                var html = '';
                plugins.forEach(function(plugin) {
                    var status = plugin.active ?
                        '<span style="color: green;">Active</span>' :
                        '<span style="color: gray;">Inactive</span>';

                    html += '<tr>';
                    html += '<td><input type="checkbox" name="plugin[]" value="' + plugin.file + '"></td>';
                    html += '<td><strong>' + plugin.name + '</strong><br>' + plugin.file + '</td>';
                    html += '<td>' + plugin.version + '</td>';
                    html += '<td>' + status + '</td>';
                    html += '<td>';

                    if (plugin.active) {
                        html += '<button class="button plugin-action" data-action="deactivate" data-plugin="' + plugin.file + '">Deactivate</button> ';
                    } else {
                        html += '<button class="button plugin-action" data-action="activate" data-plugin="' + plugin.file + '">Activate</button> ';
                        html += '<button class="button button-link-delete plugin-action" data-action="delete" data-plugin="' + plugin.file + '">Delete</button>';
                    }

                    html += '</td>';
                    html += '</tr>';
                });

                $('#installed-plugins-list').html(html || '<tr><td colspan="5">No plugins found</td></tr>');
            }

            function loadInstalledThemes() {
                $('#installed-themes-list').html('<div class="themes-loading">Loading themes...</div>');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'get_installed_themes',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success && response.data.themes) {
                        displayInstalledThemes(response.data.themes);
                    } else {
                        $('#installed-themes-list').html('<p>Error loading themes</p>');
                    }
                });
            }

            function displayInstalledThemes(themes) {
                var html = '';
                themes.forEach(function(theme) {
                    html += '<div class="theme-item' + (theme.active ? ' active' : '') + '">';
                    if (theme.screenshot) {
                        html += '<img src="' + theme.screenshot + '" class="theme-screenshot">';
                    }
                    html += '<h4>' + theme.name + '</h4>';
                    html += '<p>Version: ' + theme.version + '</p>';
                    if (theme.author) {
                        html += '<p>By ' + theme.author + '</p>';
                    }
                    if (theme.active) {
                        html += '<p><strong>Active Theme</strong></p>';
                    } else {
                        html += '<button class="button theme-action" data-action="activate" data-theme="' + theme.slug + '">Activate</button> ';
                        html += '<button class="button button-link-delete theme-action" data-action="delete" data-theme="' + theme.slug + '">Delete</button>';
                    }
                    html += '</div>';
                });

                $('#installed-themes-list').html(html || '<p>No themes found</p>');
            }

            function loadAvailablePlugins() {
                $('#available-plugins-list').html('<tr><td colspan="5">Loading available plugins from site\'s repository...</td></tr>');

                // First get site's auth config to display
                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'sync',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(syncResponse) {
                    if (syncResponse.success && syncResponse.data.cribops_data && syncResponse.data.cribops_data.repository_info) {
                        var repoInfo = syncResponse.data.cribops_data.repository_info;
                        var infoText = 'Repository: ' + repoInfo.api_url;
                        if (repoInfo.using_bearer) {
                            infoText += ' | Using Bearer Token Authentication';
                        } else {
                            infoText += ' | Using Email/Password Authentication';
                        }
                        $('#repo-info').html(infoText);
                    }

                    // Now get available plugins from the site's repository
                    $.post(ajaxurl, {
                        action: 'mainwp_cribops_run_action',
                        site_id: siteId,
                        action_type: 'get_available_plugins',
                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                    }, function(response) {
                        if (response.success && response.data.plugins) {
                            displayAvailablePlugins(response.data.plugins);
                        } else {
                            $('#available-plugins-list').html('<tr><td colspan="5">Error loading available plugins. Site may not be configured properly.</td></tr>');
                        }
                    });
                });
            }

            function generatePluginRow(plugin) {
                var statusText = '';
                var statusClass = '';
                var actions = '';

                // Determine status and available actions based on plugin state
                if (plugin.status === 'active') {
                    statusText = '<span class="dashicons dashicons-yes-alt"></span> Active';
                    statusClass = 'status-active';
                    actions = '<button class="button plugin-action" data-action="deactivate" data-plugin="' + plugin.slug + '">Deactivate</button>';
                    actions += ' <button class="button plugin-action" data-action="delete" data-plugin="' + plugin.slug + '">Delete</button>';
                } else if (plugin.status === 'inactive') {
                    statusText = '<span class="dashicons dashicons-minus"></span> Inactive';
                    statusClass = 'status-inactive';
                    actions = '<button class="button plugin-action" data-action="activate" data-plugin="' + plugin.slug + '">Activate</button>';
                    actions += ' <button class="button plugin-action" data-action="delete" data-plugin="' + plugin.slug + '">Delete</button>';
                } else if (plugin.status === 'downloaded' || plugin.local) {
                    statusText = '<span class="dashicons dashicons-download"></span> Downloaded';
                    statusClass = 'status-downloaded';
                    actions = '<button class="button button-primary plugin-action" data-action="install" data-plugin="' + plugin.slug + '">Install</button>';
                    actions += ' <button class="button plugin-action" data-action="redownload" data-plugin="' + plugin.slug + '">Re-download</button>';
                } else {
                    statusText = '<span class="dashicons dashicons-cloud"></span> Available';
                    statusClass = 'status-available';
                    actions = '<button class="button button-primary plugin-action" data-action="download" data-plugin="' + plugin.slug + '">Download</button>';
                }

                var html = '<tr data-plugin-slug="' + plugin.slug + '" data-plugin-name="' + plugin.name.toLowerCase() + '">';
                html += '<td><input type="checkbox" name="available[]" value="' + plugin.slug + '" ' + (plugin.status === 'active' ? 'disabled' : '') + '></td>';
                html += '<td><strong>' + plugin.name + '</strong><br><span style="color: #666;">' + plugin.slug + '</span>';
                if (plugin.description) {
                    html += '<div style="font-size: 12px; color: #666; margin-top: 4px;">' + plugin.description + '</div>';
                }
                html += '</td>';
                html += '<td>' + (plugin.version || '-') + '</td>';
                html += '<td class="' + statusClass + '">' + statusText + '</td>';
                html += '<td>' + actions + '</td>';
                html += '</tr>';

                return html;
            }

            function displayAvailablePlugins(plugins) {
                availablePluginsData = plugins;
                var html = '';
                plugins.forEach(function(plugin) {
                    html += generatePluginRow(plugin);
                });

                $('#available-plugins-list').html(html || '<tr><td colspan="5">No plugins available</td></tr>');
            }

            function loadAvailableThemes() {
                $('#available-themes-list').html('<div class="themes-loading">Loading available themes from site\'s repository...</div>');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'get_available_themes',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success && response.data.themes) {
                        displayAvailableThemes(response.data.themes);
                    } else {
                        $('#available-themes-list').html('<p>Error loading available themes. Site may not be configured properly.</p>');
                    }
                });
            }

            function generateThemeItem(theme) {
                var html = '<div class="theme-item' + (theme.installed ? ' installed' : '') + '" data-theme-slug="' + theme.slug + '" data-theme-name="' + theme.name.toLowerCase() + '">';
                if (theme.screenshot) {
                    html += '<img src="' + theme.screenshot + '" class="theme-screenshot">';
                }
                html += '<h4>' + theme.name + '</h4>';
                html += '<p>Version: ' + theme.version + '</p>';
                if (theme.author) {
                    html += '<p>By ' + theme.author + '</p>';
                }
                if (theme.installed) {
                    if (theme.active) {
                        html += '<p><strong style="color: green;">Active Theme</strong></p>';
                    } else {
                        html += '<p><strong style="color: orange;">Installed</strong></p>';
                    }
                } else {
                    html += '<button class="button button-primary theme-install-action" data-theme="' + theme.slug + '">Install Theme</button>';
                }
                html += '</div>';
                return html;
            }

            function displayAvailableThemes(themes) {
                availableThemesData = themes;
                var html = '';
                themes.forEach(function(theme) {
                    html += generateThemeItem(theme);
                });

                $('#available-themes-list').html(html || '<p>No themes available</p>');
            }

            function loadPackages() {
                $('#packages-list').html('<tr><td colspan="4">Loading packages from site\'s repository...</td></tr>');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'get_available_packages',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success && response.data.packages) {
                        displayPackages(response.data.packages);
                    } else {
                        $('#packages-list').html('<tr><td colspan="4">No packages available or error loading packages.</td></tr>');
                    }
                });
            }

            function generatePackageRow(pkg) {
                var html = '<tr data-package-slug="' + pkg.slug + '" data-package-name="' + pkg.name.toLowerCase() + '">';
                html += '<td><strong>' + pkg.name + '</strong></td>';
                html += '<td>' + (pkg.description || 'No description available') + '</td>';
                html += '<td>' + (pkg.size || 'Unknown') + '</td>';
                html += '<td>';
                html += '<button class="button package-action" data-action="download" data-package="' + pkg.slug + '">Download</button>';
                html += '</td>';
                html += '</tr>';
                return html;
            }

            function displayPackages(packages) {
                packagesData = packages;
                var html = '';
                packages.forEach(function(pkg) {
                    html += generatePackageRow(pkg);
                });

                $('#packages-list').html(html || '<tr><td colspan="4">No packages found</td></tr>');
            }

            function loadSettings() {
                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'sync',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success && response.data.cribops_data) {
                        var data = response.data.cribops_data;

                        // Display repository configuration
                        if (data.repository_info) {
                            $('#site-auth-type').text(data.repository_info.using_bearer ? 'Bearer Token' : 'Email/Password');
                            $('#site-api-endpoint').text(data.repository_info.api_url);
                            $('#site-repo-status').html(data.repository_info.configured ?
                                '<span style="color: green;">✓ Configured</span>' :
                                '<span style="color: red;">✗ Not Configured</span>');

                            if (data.repository_info.using_bearer) {
                                $('#site-bearer-status').html('<span style="color: green;">✓ Using Bearer Token</span>');
                            } else {
                                $('#site-bearer-status').text('Not using bearer authentication');
                            }
                        } else {
                            $('#site-auth-type').text('Not configured');
                            $('#site-api-endpoint').text('Not configured');
                            $('#site-repo-status').html('<span style="color: red;">✗ Not Configured</span>');
                            $('#site-bearer-status').text('N/A');
                        }

                        // Display auth status
                        if (data.auth_status) {
                            var authStatusText = data.auth_status === 'authenticated' ?
                                '<span style="color: green;">✓ Authenticated</span>' :
                                '<span style="color: red;">✗ Not Authenticated</span>';
                            $('#site-repo-status').append(' | ' + authStatusText);
                        }
                    } else {
                        $('#site-auth-type').text('Unable to load');
                        $('#site-api-endpoint').text('Unable to load');
                        $('#site-repo-status').text('Unable to load');
                        $('#site-bearer-status').text('Unable to load');
                    }
                });
            }

            function loadActivityLogs() {
                $('#activity-logs-list').html('<p>Loading activity logs...</p>');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'get_logs',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success && response.data.logs) {
                        displayActivityLogs(response.data.logs);
                    } else {
                        $('#activity-logs-list').html('<p>No logs available</p>');
                    }
                });
            }

            function displayActivityLogs(logs) {
                var html = '';
                logs.reverse().forEach(function(log) {
                    html += '<div class="log-entry">';
                    html += '<strong>' + log.timestamp + '</strong> - ';
                    html += log.action + ': ' + log.details;
                    if (log.user) {
                        html += ' (by ' + log.user + ')';
                    }
                    html += '</div>';
                });

                $('#activity-logs-list').html(html || '<p>No activity logs found</p>');
            }

            // Plugin action handlers
            $(document).on('click', '.plugin-action', function() {
                var $button = $(this);
                var action = $button.data('action');
                var plugin = $button.data('plugin');

                $button.prop('disabled', true).text('Processing...');

                var ajaxAction = 'cribops_' + action + '_plugin';

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: action + '_plugin',
                    args: { plugin_file: plugin },
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        loadInstalledPlugins();
                    } else {
                        alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                    }
                    $button.prop('disabled', false).text(action.charAt(0).toUpperCase() + action.slice(1));
                });
            });

            // Plugin actions (download, install, activate, deactivate, delete, redownload)
            $(document).on('click', '.plugin-action', function() {
                var $button = $(this);
                var action = $button.data('action');
                var plugin = $button.data('plugin');
                var originalText = $button.text();
                var $row = $button.closest('tr');

                // Map action to child site method
                var actionMap = {
                    'download': 'download_plugin',
                    'install': 'install_plugin',
                    'activate': 'activate_plugin',
                    'deactivate': 'deactivate_plugin',
                    'delete': 'delete_plugin',
                    'redownload': 'download_plugin'
                };

                var actionType = actionMap[action];
                if (!actionType) {
                    alert('Unknown action: ' + action);
                    return;
                }

                // Confirm delete action
                if (action === 'delete' && !confirm('Are you sure you want to delete this plugin?')) {
                    return;
                }

                $button.prop('disabled', true).text(action.charAt(0).toUpperCase() + action.slice(1) + 'ing...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    timeout: 120000, // 2 minute timeout for long operations
                    data: {
                        action: 'mainwp_cribops_run_action',
                        site_id: siteId,
                        action_type: actionType,
                        args: {
                            plugin_slug: plugin
                        },
                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                    },
                    success: function(response) {
                        console.log(action + ' response:', response);

                        // Check for errors
                        if (!response.success || (response.data && response.data.error)) {
                            var errorMsg = 'Unknown error';
                            if (response.data && response.data.error) {
                                errorMsg = response.data.error;
                            } else if (response.data && typeof response.data === 'string') {
                                errorMsg = response.data;
                            }
                            alert('Error: ' + errorMsg);
                            $button.prop('disabled', false).text(originalText);
                            return;
                        }

                        // Success - wait a moment for the operation to complete, then fetch fresh data
                        $button.text('Refreshing...');
                        setTimeout(function() {
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                timeout: 30000,
                                data: {
                                    action: 'mainwp_cribops_run_action',
                                    site_id: siteId,
                                    action_type: 'get_available_plugins',
                                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                                },
                                success: function(freshResponse) {
                                    if (freshResponse.success && freshResponse.data.plugins) {
                                        // Find the updated plugin data
                                        var updatedPlugin = freshResponse.data.plugins.find(function(p) {
                                            return p.slug === plugin;
                                        });

                                        if (updatedPlugin) {
                                            // Update the stored data
                                            var index = availablePluginsData.findIndex(function(p) {
                                                return p.slug === plugin;
                                            });
                                            if (index !== -1) {
                                                availablePluginsData[index] = updatedPlugin;
                                            }

                                            // Replace only this row
                                            var newRow = generatePluginRow(updatedPlugin);
                                            $row.replaceWith(newRow);
                                        }
                                    }

                                    // Also refresh installed plugins if that tab is visible
                                    if ($('#plugins').hasClass('active')) {
                                        loadInstalledPlugins();
                                    }
                                },
                                error: function() {
                                    // If refresh fails, just reload the whole list
                                    loadAvailablePlugins();
                                    if ($('#plugins').hasClass('active')) {
                                        loadInstalledPlugins();
                                    }
                                }
                            });
                        }, 1500); // Wait 1.5 seconds for operation to fully complete on server
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);

                        if (textStatus === 'timeout') {
                            // Operation timed out - show message and refresh to get actual status
                            $button.text('Checking status...');
                            alert('Operation is taking longer than expected. Checking current status...');

                            setTimeout(function() {
                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    timeout: 30000,
                                    data: {
                                        action: 'mainwp_cribops_run_action',
                                        site_id: siteId,
                                        action_type: 'get_available_plugins',
                                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                                    },
                                    success: function(freshResponse) {
                                        if (freshResponse.success && freshResponse.data.plugins) {
                                            var updatedPlugin = freshResponse.data.plugins.find(function(p) {
                                                return p.slug === plugin;
                                            });

                                            if (updatedPlugin) {
                                                var index = availablePluginsData.findIndex(function(p) {
                                                    return p.slug === plugin;
                                                });
                                                if (index !== -1) {
                                                    availablePluginsData[index] = updatedPlugin;
                                                }

                                                var newRow = generatePluginRow(updatedPlugin);
                                                $row.replaceWith(newRow);
                                            }
                                        }

                                        if ($('#plugins').hasClass('active')) {
                                            loadInstalledPlugins();
                                        }
                                    },
                                    error: function() {
                                        alert('Unable to verify status. Please refresh the page to see current state.');
                                        $button.prop('disabled', false).text(originalText);
                                    }
                                });
                            }, 2000);
                        } else {
                            alert('Communication error: ' + textStatus);
                            $button.prop('disabled', false).text(originalText);
                        }
                    }
                });
            });

            // Theme action handlers
            $(document).on('click', '.theme-action', function() {
                var $button = $(this);
                var action = $button.data('action');
                var theme = $button.data('theme');

                $button.prop('disabled', true).text('Processing...');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: action + '_theme',
                    args: { theme_slug: theme },
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        loadInstalledThemes();
                    } else {
                        alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                    }
                    $button.prop('disabled', false).text(action.charAt(0).toUpperCase() + action.slice(1));
                });
            });

            // Settings refresh button handler
            $('#refresh-settings').on('click', function() {
                loadSettings();
            });

            // Sync site button handler
            $('#sync-site').on('click', function() {
                var $button = $(this);
                $button.prop('disabled', true).text('Syncing...');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'sync',
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        loadSettings();
                        alert('Site synchronized successfully');
                    } else {
                        alert('Error syncing site');
                    }
                    $button.prop('disabled', false).text('Sync with Site');
                });
            });

            // Refresh button handlers
            $('#refresh-plugins').on('click', loadInstalledPlugins);
            $('#refresh-themes').on('click', loadInstalledThemes);
            $('#refresh-available-plugins').on('click', loadAvailablePlugins);
            $('#refresh-available-themes').on('click', loadAvailableThemes);
            $('#refresh-packages').on('click', loadPackages);
            $('#refresh-logs').on('click', loadActivityLogs);

            // Theme install action handler
            $(document).on('click', '.theme-install-action', function() {
                var $button = $(this);
                var theme = $button.data('theme');
                var $themeItem = $button.closest('.theme-item');

                $button.prop('disabled', true).text('Installing...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    timeout: 120000, // 2 minute timeout
                    data: {
                        action: 'mainwp_cribops_run_action',
                        site_id: siteId,
                        action_type: 'install_theme',
                        args: { theme_slug: theme },
                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update only this theme item by fetching fresh data
                            $button.text('Refreshing...');
                            setTimeout(function() {
                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    timeout: 30000,
                                    data: {
                                        action: 'mainwp_cribops_run_action',
                                        site_id: siteId,
                                        action_type: 'get_available_themes',
                                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                                    },
                                    success: function(freshResponse) {
                                        if (freshResponse.success && freshResponse.data.themes) {
                                            // Find the updated theme data
                                            var updatedTheme = freshResponse.data.themes.find(function(t) {
                                                return t.slug === theme;
                                            });

                                            if (updatedTheme) {
                                                // Update the stored data
                                                var index = availableThemesData.findIndex(function(t) {
                                                    return t.slug === theme;
                                                });
                                                if (index !== -1) {
                                                    availableThemesData[index] = updatedTheme;
                                                }

                                                // Replace only this theme item
                                                var newThemeItem = generateThemeItem(updatedTheme);
                                                $themeItem.replaceWith(newThemeItem);
                                            }
                                        }

                                        // Also refresh installed themes if that tab is visible
                                        if ($('#themes').hasClass('active')) {
                                            loadInstalledThemes();
                                        }
                                    },
                                    error: function() {
                                        loadAvailableThemes();
                                        if ($('#themes').hasClass('active')) {
                                            loadInstalledThemes();
                                        }
                                    }
                                });
                            }, 1500);
                        } else {
                            alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                            $button.prop('disabled', false).text('Install Theme');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (textStatus === 'timeout') {
                            $button.text('Checking status...');
                            alert('Installation is taking longer than expected. Checking current status...');

                            setTimeout(function() {
                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    timeout: 30000,
                                    data: {
                                        action: 'mainwp_cribops_run_action',
                                        site_id: siteId,
                                        action_type: 'get_available_themes',
                                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                                    },
                                    success: function(freshResponse) {
                                        if (freshResponse.success && freshResponse.data.themes) {
                                            var updatedTheme = freshResponse.data.themes.find(function(t) {
                                                return t.slug === theme;
                                            });

                                            if (updatedTheme) {
                                                var index = availableThemesData.findIndex(function(t) {
                                                    return t.slug === theme;
                                                });
                                                if (index !== -1) {
                                                    availableThemesData[index] = updatedTheme;
                                                }

                                                var newThemeItem = generateThemeItem(updatedTheme);
                                                $themeItem.replaceWith(newThemeItem);
                                            }
                                        }

                                        if ($('#themes').hasClass('active')) {
                                            loadInstalledThemes();
                                        }
                                    },
                                    error: function() {
                                        alert('Unable to verify status. Please refresh the page to see current state.');
                                        $button.prop('disabled', false).text('Install Theme');
                                    }
                                });
                            }, 2000);
                        } else {
                            alert('Communication error: ' + textStatus);
                            $button.prop('disabled', false).text('Install Theme');
                        }
                    }
                });
            });

            // Bulk action handlers
            $('#bulk-activate, #bulk-deactivate, #bulk-delete').on('click', function() {
                var action = $(this).attr('id').replace('bulk-', '');
                var selected = [];

                $('#installed-plugins-list input[type="checkbox"]:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    alert('Please select plugins to ' + action);
                    return;
                }

                if (action === 'delete' && !confirm('Are you sure you want to delete the selected plugins?')) {
                    return;
                }

                selected.forEach(function(plugin) {
                    $.post(ajaxurl, {
                        action: 'mainwp_cribops_run_action',
                        site_id: siteId,
                        action_type: action + '_plugin',
                        args: { plugin_file: plugin },
                        nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                    }, function(response) {
                        loadInstalledPlugins();
                    });
                });
            });

            // Select all checkbox
            $('#cb-select-all-plugins').on('change', function() {
                $('#installed-plugins-list input[type="checkbox"]').prop('checked', $(this).is(':checked'));
            });

            $('#cb-select-all-available-plugins').on('change', function() {
                $('#available-plugins-list input[type="checkbox"]').prop('checked', $(this).is(':checked'));
            });

            // Package action handler
            $(document).on('click', '.package-action', function() {
                var $button = $(this);
                var action = $button.data('action');
                var packageSlug = $button.data('package');
                var $row = $button.closest('tr');

                $button.prop('disabled', true).text('Downloading...');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'download_package',
                    args: { package_slug: packageSlug },
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        // Update only this row by fetching fresh data
                        $.post(ajaxurl, {
                            action: 'mainwp_cribops_run_action',
                            site_id: siteId,
                            action_type: 'get_available_packages',
                            nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                        }, function(freshResponse) {
                            if (freshResponse.success && freshResponse.data.packages) {
                                // Find the updated package data
                                var updatedPackage = freshResponse.data.packages.find(function(p) {
                                    return p.slug === packageSlug;
                                });

                                if (updatedPackage) {
                                    // Update the stored data
                                    var index = packagesData.findIndex(function(p) {
                                        return p.slug === packageSlug;
                                    });
                                    if (index !== -1) {
                                        packagesData[index] = updatedPackage;
                                    }

                                    // Replace only this row
                                    var newRow = generatePackageRow(updatedPackage);
                                    $row.replaceWith(newRow);
                                }
                            }
                        });
                        alert('Package downloaded successfully');
                    } else {
                        alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                        $button.prop('disabled', false).text('Download');
                    }
                });
            });

            // Search functionality for available plugins
            $('#search-available-plugins').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('#available-plugins-list tr').each(function() {
                    var $row = $(this);
                    var pluginName = $row.data('plugin-name');
                    if (pluginName) {
                        if (pluginName.indexOf(searchTerm) > -1) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    }
                });
            });

            // Search functionality for available themes
            $('#search-available-themes').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('#available-themes-list .theme-item').each(function() {
                    var $item = $(this);
                    var themeName = $item.data('theme-name');
                    if (themeName) {
                        if (themeName.indexOf(searchTerm) > -1) {
                            $item.show();
                        } else {
                            $item.hide();
                        }
                    }
                });
            });

            // Search functionality for packages
            $('#search-packages').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('#packages-list tr').each(function() {
                    var $row = $(this);
                    var packageName = $row.data('package-name');
                    if (packageName) {
                        if (packageName.indexOf(searchTerm) > -1) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    }
                });
            });
        });
        </script>
        <?php
        // Add MainWP page footer
        do_action('mainwp_pagefooter_sites', 'cribops-wp-kit');
    }

    /**
     * Get site information
     */
    private static function get_site_info($site_id) {
        if (class_exists('MainWP\Dashboard\MainWP_DB')) {
            return \MainWP\Dashboard\MainWP_DB::instance()->get_website_by_id($site_id);
        }
        return null;
    }
}