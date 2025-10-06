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
        ?>
        <div class="wrap mainwp-cribops-site-management">
            <h2>Manage CribOps WP Kit - <?php echo esc_html($site->name); ?></h2>

            <div class="nav-tab-wrapper">
                <a href="#plugins" class="nav-tab nav-tab-active" data-tab="plugins">Plugins</a>
                <a href="#themes" class="nav-tab" data-tab="themes">Themes</a>
                <a href="#available" class="nav-tab" data-tab="available">Available from CribOps</a>
                <a href="#settings" class="nav-tab" data-tab="settings">Settings</a>
                <a href="#logs" class="nav-tab" data-tab="logs">Activity Logs</a>
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

            <div id="available" class="tab-content">
                <h3>Available Plugins from Site's Repository</h3>
                <div class="notice notice-info">
                    <p id="repo-info">Loading repository information...</p>
                </div>
                <div class="cribops-toolbar">
                    <button class="button" id="refresh-available">Refresh Available</button>
                    <select id="plugin-recipe">
                        <option value="">-- Install Recipe --</option>
                        <option value="essential">Essential Plugins</option>
                        <option value="security">Security Suite</option>
                        <option value="performance">Performance Pack</option>
                        <option value="ecommerce">E-commerce Bundle</option>
                    </select>
                    <button class="button button-primary" id="install-recipe">Install Recipe</button>
                </div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all-available">
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
            .mainwp-cribops-site-management .tab-content {
                display: none;
                padding: 20px 0;
            }
            .mainwp-cribops-site-management .tab-content.active {
                display: block;
            }
            .cribops-toolbar {
                margin-bottom: 15px;
            }
            .cribops-toolbar button,
            .cribops-toolbar select {
                margin-right: 10px;
            }
            .theme-browser {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }
            .theme-item {
                border: 1px solid #ddd;
                padding: 10px;
                width: 300px;
            }
            .theme-item.active {
                border-color: #0073aa;
            }
            .theme-screenshot {
                width: 100%;
                height: auto;
            }
            #activity-logs-list {
                max-height: 500px;
                overflow-y: auto;
                border: 1px solid #ddd;
                padding: 10px;
            }
            .log-entry {
                padding: 5px 0;
                border-bottom: 1px solid #eee;
            }
            .log-entry:last-child {
                border-bottom: none;
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            var siteId = <?php echo intval($site_id); ?>;

            // Tab switching
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');

                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                $('.tab-content').removeClass('active');
                $('#' + tab).addClass('active');

                // Load data for the tab if needed
                if (tab === 'plugins' && $('#installed-plugins-list tr').length === 1) {
                    loadInstalledPlugins();
                } else if (tab === 'themes' && $('.themes-loading').length) {
                    loadInstalledThemes();
                } else if (tab === 'available' && $('#available-plugins-list tr').length === 1) {
                    loadAvailablePlugins();
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
                    if (syncResponse.success && syncResponse.data.cribops_data && syncResponse.data.cribops_data.auth_config) {
                        var authConfig = syncResponse.data.cribops_data.auth_config;
                        var repoInfo = 'Repository: ' + authConfig.api_endpoint + ' | Auth: ' + authConfig.auth_type;
                        if (authConfig.auth_type === 'bearer') {
                            repoInfo += ' (Token ' + (authConfig.has_bearer_token ? 'configured' : 'not configured') + ')';
                        }
                        $('#repo-info').html(repoInfo);
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

            function displayAvailablePlugins(plugins) {
                var html = '';
                plugins.forEach(function(plugin) {
                    var status = '';
                    if (plugin.installed) {
                        if (plugin.active) {
                            status = '<span style="color: green;">Active</span>';
                        } else {
                            status = '<span style="color: orange;">Installed</span>';
                        }
                        if (plugin.update_available) {
                            status += ' <span style="color: blue;">(Update Available)</span>';
                        }
                    } else {
                        status = '<span style="color: gray;">Not Installed</span>';
                    }

                    html += '<tr>';
                    html += '<td><input type="checkbox" name="available[]" value="' + plugin.slug + '"></td>';
                    html += '<td><strong>' + plugin.name + '</strong><br>' + plugin.slug + '</td>';
                    html += '<td>' + plugin.version + '</td>';
                    html += '<td>' + status + '</td>';
                    html += '<td>';

                    if (!plugin.installed) {
                        html += '<button class="button button-primary available-action" data-action="install" data-plugin="' + plugin.slug + '">Install</button>';
                    } else if (plugin.update_available) {
                        html += '<button class="button available-action" data-action="update" data-plugin="' + plugin.slug + '">Update</button>';
                    }

                    html += '</td>';
                    html += '</tr>';
                });

                $('#available-plugins-list').html(html || '<tr><td colspan="5">No plugins available</td></tr>');
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

                        // Display auth configuration
                        if (data.auth_config) {
                            $('#site-auth-type').text(data.auth_config.auth_type === 'bearer' ? 'Bearer Token' : 'Email/Password');
                            $('#site-api-endpoint').text(data.auth_config.api_endpoint);
                            $('#site-repo-status').html(data.auth_config.repository_configured ?
                                '<span style="color: green;">✓ Configured</span>' :
                                '<span style="color: red;">✗ Not Configured</span>');

                            if (data.auth_config.auth_type === 'bearer') {
                                $('#site-bearer-status').html(data.auth_config.has_bearer_token ?
                                    '<span style="color: green;">✓ Token Set</span>' :
                                    '<span style="color: orange;">⚠ Token Not Set</span>');
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

            // Available plugin installation
            $(document).on('click', '.available-action', function() {
                var $button = $(this);
                var action = $button.data('action');
                var plugin = $button.data('plugin');

                $button.prop('disabled', true).text('Installing...');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'install_single_plugin',
                    args: {
                        plugin_slug: plugin,
                        activate: true
                    },
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        loadAvailablePlugins();
                        // Also refresh installed plugins if that tab is visible
                        if ($('#plugins').hasClass('active')) {
                            loadInstalledPlugins();
                        }
                    } else {
                        alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                    }
                    $button.prop('disabled', false).text('Install');
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
            $('#refresh-available').on('click', loadAvailablePlugins);
            $('#refresh-logs').on('click', loadActivityLogs);

            // Install recipe handler
            $('#install-recipe').on('click', function() {
                var recipe = $('#plugin-recipe').val();
                if (!recipe) {
                    alert('Please select a recipe');
                    return;
                }

                $(this).prop('disabled', true).text('Installing...');

                $.post(ajaxurl, {
                    action: 'mainwp_cribops_run_action',
                    site_id: siteId,
                    action_type: 'install_plugins',
                    args: { recipe: recipe },
                    nonce: '<?php echo wp_create_nonce('mainwp-cribops-nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        alert('Recipe installed successfully');
                        loadAvailablePlugins();
                    } else {
                        alert('Error: ' + (response.data && response.data.error ? response.data.error : 'Unknown error'));
                    }
                    $('#install-recipe').prop('disabled', false).text('Install Recipe');
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

            $('#cb-select-all-available').on('change', function() {
                $('#available-plugins-list input[type="checkbox"]').prop('checked', $(this).is(':checked'));
            });
        });
        </script>
        <?php
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