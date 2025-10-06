# Testing Guide for MainWP CribOps Extension

## Prerequisites

1. MainWP Dashboard site installed at `~/dev/mwp`
2. Child site with CribOps WP Kit installed at `~/dev/cribops-wpdemo`
3. MainWP Child plugin installed and connected on the child site

## Installation Steps

### 1. Install Updated CribOps WP Kit on Child Site

Copy the updated child integration file to your demo site:
```bash
cp ~/dev/cribops-wp-kit/includes/class-cwpk-mainwp-child.php ~/dev/cribops-wpdemo/wp-content/plugins/cribops-wp-kit/includes/
```

### 2. Install MainWP Extension on Dashboard Site

Copy the extension to MainWP site:
```bash
cp -r ~/dev/mainwp-cribops-extension ~/dev/mwp/wp-content/plugins/
```

### 3. Activate the Extension

1. Go to MainWP Dashboard admin
2. Navigate to Plugins
3. Activate "MainWP CribOps Extension"

## Testing the Integration

### Basic Functionality Test

1. **Access Extension Page**
   - Go to MainWP → Extensions → CribOps WP Kit
   - You should see a list of connected sites

2. **Sync Site Data**
   - Click "Sync" button for a site
   - Status should update showing CribOps WP Kit version

3. **Individual Site Management**
   - Click "Manage" button for a site
   - This opens the enhanced management interface

### Plugin Management Tests

1. **View Installed Plugins**
   - Click "Manage" on a site
   - The Plugins tab should show all installed plugins
   - Status (Active/Inactive) should be visible

2. **Activate/Deactivate Plugins**
   - Click "Deactivate" on an active plugin
   - Plugin should deactivate
   - Click "Activate" to reactivate

3. **Install New Plugin**
   - Go to "Available from CribOps" tab
   - Select a plugin not installed
   - Click "Install"
   - Plugin should install and activate

4. **Bulk Operations**
   - Select multiple plugins using checkboxes
   - Use "Bulk Activate" or "Bulk Deactivate"

### Theme Management Tests

1. **View Themes**
   - Click "Themes" tab
   - Should show all installed themes
   - Active theme should be highlighted

2. **Activate Theme**
   - Click "Activate" on an inactive theme
   - Theme should become active

### Recipe Installation Test

1. **Install Plugin Recipe**
   - Go to "Available from CribOps" tab
   - Select a recipe from dropdown (e.g., "Essential Plugins")
   - Click "Install Recipe"
   - All plugins in recipe should install

### Settings Management

1. **View Settings**
   - Click "Settings" tab
   - Current settings should load

2. **Update Settings**
   - Change authentication type
   - Enter bearer token if using bearer auth
   - Click "Save Settings"

### Activity Logs

1. **View Logs**
   - Click "Activity Logs" tab
   - Should show recent activities
   - Each action performed should be logged

## API Endpoints Being Tested

The following CribOps WP Kit endpoints are exercised:

- `cribops_get_status` - Get plugin status
- `cribops_get_installed_plugins` - List installed plugins
- `cribops_get_available_plugins` - List available plugins from repository
- `cribops_get_installed_themes` - List themes
- `cribops_install_single_plugin` - Install a plugin
- `cribops_activate_plugin` - Activate a plugin
- `cribops_deactivate_plugin` - Deactivate a plugin
- `cribops_delete_plugin` - Delete a plugin
- `cribops_activate_theme` - Activate a theme
- `cribops_delete_theme` - Delete a theme
- `cribops_get_settings` - Get settings
- `cribops_update_settings` - Update settings
- `cribops_get_logs` - Get activity logs
- `cribops_install_plugins` - Install plugin recipe

## Troubleshooting

### If Communication Fails

1. Check MainWP Child is connected:
   ```
   MainWP → Sites → [Your Site] → Edit
   ```

2. Verify CribOps WP Kit is active on child site

3. Check browser console for JavaScript errors

4. Ensure both plugins are updated to latest versions

### Debug Mode

Add to wp-config.php for debugging:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check logs at:
- MainWP: `~/dev/mwp/wp-content/debug.log`
- Child: `~/dev/cribops-wpdemo/wp-content/debug.log`

## Expected Results

After successful testing, you should be able to:

1. View all plugins/themes on child sites from MainWP dashboard
2. Install/activate/deactivate/delete plugins remotely
3. Switch themes on child sites
4. View CribOps available plugins repository
5. Install plugin bundles/recipes
6. Track all actions in activity logs
7. Manage CribOps WP Kit settings remotely

## Next Steps

Once basic testing is complete, consider:

1. Testing with multiple child sites
2. Testing premium plugin installation (if licenses configured)
3. Testing with different user roles
4. Performance testing with sites having many plugins