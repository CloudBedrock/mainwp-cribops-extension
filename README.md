# MainWP CribOps Extension

A MainWP Extension that enables centralized control and management of CribOps WP Kit across multiple WordPress sites.

## Overview

This extension allows MainWP dashboard administrators to:
- Monitor CribOps WP Kit installation status across all child sites
- Trigger bulk plugin installations using predefined recipes
- Manage premium plugin licenses centrally
- Synchronize CribOps WP Kit settings across sites
- View installation logs and activity reports

## Requirements

### Dashboard Site (MainWP Server)
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MainWP Dashboard plugin (latest version)
- This MainWP CribOps Extension

### Child Sites
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MainWP Child plugin (latest version)
- CribOps WP Kit with MainWP integration (v1.1.3+)

## Installation

### Step 1: Install on MainWP Dashboard

1. Upload the `mainwp-cribops-extension` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to MainWP → CribOps WP Kit

### Step 2: Prepare Child Sites

1. Ensure MainWP Child plugin is installed and connected
2. Install CribOps WP Kit (with MainWP integration support)
3. The integration will automatically activate when both plugins are present

## Features

### Site Status Monitoring
- Real-time status of CribOps WP Kit on all child sites
- Version tracking
- Authentication status
- Settings overview

### Bulk Plugin Installation
Pre-configured recipes:
- **Essential Plugins**: Classic Editor, Duplicate Post, Regenerate Thumbnails, WP Mail SMTP
- **Security Suite**: Wordfence, Limit Login Attempts, WP Force SSL
- **Performance Pack**: WP Super Cache, Autoptimize, WP Sweep
- **E-commerce Bundle**: WooCommerce and related plugins

### License Management
- Centrally manage premium plugin licenses
- Push license keys to all child sites
- Support for major premium plugins (ACF Pro, Gravity Forms, etc.)

### Settings Synchronization
- Push CribOps WP Kit settings to multiple sites
- Bulk configuration updates
- Template-based settings management

## Available Remote Functions

The following functions can be called on child sites:

- `cribops_get_status`: Get plugin status and configuration
- `cribops_get_settings`: Retrieve current settings
- `cribops_update_settings`: Update plugin settings
- `cribops_install_plugins`: Install plugin recipes
- `cribops_get_installed_plugins`: List installed plugins
- `cribops_manage_licenses`: Manage license keys
- `cribops_get_logs`: Retrieve activity logs
- `cribops_run_bulk_install`: Execute bulk installations
- `cribops_sync`: Synchronize with dashboard

## API Integration

The extension communicates with child sites using MainWP's secure API:
- Authentication via MainWP signatures
- HTTPS encrypted communication
- WordPress nonce verification
- Capability checks on both sides

## Development

### File Structure
```
mainwp-cribops-extension/
├── mainwp-cribops-extension.php    # Main plugin file
├── class-mainwp-cribops-extension.php  # Extension class
├── assets/
│   ├── admin.js                    # Admin JavaScript
│   └── admin.css                   # Admin styles
└── README.md                        # This file
```

### Hooks and Filters

#### Dashboard Hooks
- `mainwp_getextensions`: Register the extension
- `mainwp_sync_others_data`: Add custom sync data
- `mainwp_site_synced`: Handle post-sync actions

#### Child Site Hooks
- `mainwp_child_extra_execution`: Handle dashboard requests
- `mainwp_site_sync_others_data`: Add data to sync
- `mainwp_child_callable_functions`: Register functions

## Testing

### Basic Communication Test

1. **Dashboard Side**:
   - Navigate to MainWP → CribOps WP Kit
   - Click "Sync All Sites"
   - Verify site status updates

2. **Child Site Verification**:
   - Check if CribOps WP Kit shows MainWP integration active
   - Review activity logs for MainWP requests

### Installation Test

1. Select a test recipe (e.g., Essential Plugins)
2. Click "Install on All Sites"
3. Monitor progress in the dashboard
4. Verify installations on child sites

## Troubleshooting

### Extension Not Appearing in MainWP
- Verify MainWP Dashboard is active
- Check PHP error logs
- Ensure proper file permissions

### Child Sites Not Responding
- Verify MainWP Child is installed and connected
- Check CribOps WP Kit has MainWP integration loaded
- Review child site error logs

### Installation Failures
- Verify authentication credentials
- Check API endpoint availability
- Ensure sufficient server resources

## Support

For issues or questions:
- GitHub: https://github.com/CloudBedrock/mainwp-cribops-extension
- Email: support@cribops.com

## License

GPL v2 or later

## Credits

Developed by CribOps Development Team
Based on MainWP Extension architecture