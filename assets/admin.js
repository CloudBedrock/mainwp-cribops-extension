/**
 * MainWP CribOps Extension Admin JavaScript
 */

jQuery(document).ready(function($) {
    // Load sites on page load
    loadCribOpsSites();

    // Sync all sites
    $('#cribops-sync-all').on('click', function() {
        var $button = $(this);
        $button.prop('disabled', true).text('Syncing...');

        $.post(mainwpCribOps.ajaxurl, {
            action: 'mainwp_cribops_run_action',
            action_type: 'sync_all',
            nonce: mainwpCribOps.nonce
        }, function(response) {
            if (response.success) {
                loadCribOpsSites();
                alert('Sites synced successfully');
            } else {
                alert('Error: ' + response.data.error);
            }
            $button.prop('disabled', false).text('Sync All Sites');
        });
    });

    // Install plugins
    $('#cribops-install-plugins').on('click', function() {
        showPluginInstallDialog();
    });

    // Manage licenses
    $('#cribops-manage-licenses').on('click', function() {
        showLicenseManagementDialog();
    });

    // Load sites function
    function loadCribOpsSites() {
        $('#cribops-sites-list').html('<tr><td colspan="4">Loading sites...</td></tr>');

        $.post(mainwpCribOps.ajaxurl, {
            action: 'mainwp_cribops_get_sites',
            nonce: mainwpCribOps.nonce
        }, function(response) {
            if (response.success) {
                displaySites(response.data);
            } else {
                $('#cribops-sites-list').html('<tr><td colspan="4">Error loading sites</td></tr>');
            }
        });
    }

    // Display sites function
    function displaySites(sites) {
        var html = '';

        if (sites.length === 0) {
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
                html += '<button class="button button-small cribops-site-action" data-site-id="' + site.id + '" data-action="install">Install Plugins</button> ';
                html += '<button class="button button-small cribops-site-action" data-site-id="' + site.id + '" data-action="settings">Settings</button>';
                html += '</td>';
                html += '</tr>';
            });
        }

        $('#cribops-sites-list').html(html);

        // Bind action buttons
        $('.cribops-site-action').on('click', function() {
            var $button = $(this);
            var siteId = $button.data('site-id');
            var action = $button.data('action');

            $button.prop('disabled', true);

            $.post(mainwpCribOps.ajaxurl, {
                action: 'mainwp_cribops_run_action',
                site_id: siteId,
                action_type: action,
                nonce: mainwpCribOps.nonce
            }, function(response) {
                if (response.success) {
                    alert('Action completed successfully');
                    if (action === 'sync') {
                        loadCribOpsSites();
                    }
                } else {
                    alert('Error: ' + (response.data.error || 'Unknown error'));
                }
                $button.prop('disabled', false);
            });
        });
    }

    // Show plugin installation dialog
    function showPluginInstallDialog() {
        var dialog = '<div id="cribops-plugin-dialog" style="display:none;">';
        dialog += '<h3>Select Plugin Recipe</h3>';
        dialog += '<p>Choose a plugin installation recipe to run on selected sites:</p>';
        dialog += '<select id="cribops-recipe-select">';
        dialog += '<option value="essential">Essential Plugins</option>';
        dialog += '<option value="security">Security Suite</option>';
        dialog += '<option value="performance">Performance Pack</option>';
        dialog += '<option value="ecommerce">E-commerce Bundle</option>';
        dialog += '</select>';
        dialog += '<br><br>';
        dialog += '<button class="button button-primary" onclick="runPluginInstall()">Install on All Sites</button>';
        dialog += '</div>';

        $(dialog).dialog({
            title: 'CribOps Plugin Installation',
            modal: true,
            width: 400,
            buttons: {
                Cancel: function() {
                    $(this).dialog('close');
                }
            }
        });
    }

    // Show license management dialog
    function showLicenseManagementDialog() {
        var dialog = '<div id="cribops-license-dialog" style="display:none;">';
        dialog += '<h3>License Management</h3>';
        dialog += '<p>Manage premium plugin licenses across your network:</p>';
        dialog += '<textarea id="cribops-license-keys" rows="10" cols="50" placeholder="Enter license keys (one per line):\nPlugin Name|License Key"></textarea>';
        dialog += '<br><br>';
        dialog += '<button class="button button-primary" onclick="saveLicenses()">Save & Push to Sites</button>';
        dialog += '</div>';

        $(dialog).dialog({
            title: 'CribOps License Management',
            modal: true,
            width: 500,
            buttons: {
                Cancel: function() {
                    $(this).dialog('close');
                }
            }
        });
    }

    // Global functions for dialogs
    window.runPluginInstall = function() {
        var recipe = $('#cribops-recipe-select').val();

        $.post(mainwpCribOps.ajaxurl, {
            action: 'mainwp_cribops_run_action',
            action_type: 'install_recipe',
            recipe: recipe,
            nonce: mainwpCribOps.nonce
        }, function(response) {
            if (response.success) {
                alert('Plugin installation started on all sites');
                $('#cribops-plugin-dialog').dialog('close');
                loadCribOpsSites();
            } else {
                alert('Error: ' + response.data.error);
            }
        });
    };

    window.saveLicenses = function() {
        var licenses = $('#cribops-license-keys').val();

        $.post(mainwpCribOps.ajaxurl, {
            action: 'mainwp_cribops_run_action',
            action_type: 'save_licenses',
            licenses: licenses,
            nonce: mainwpCribOps.nonce
        }, function(response) {
            if (response.success) {
                alert('Licenses saved and pushed to sites');
                $('#cribops-license-dialog').dialog('close');
            } else {
                alert('Error: ' + response.data.error);
            }
        });
    };
});