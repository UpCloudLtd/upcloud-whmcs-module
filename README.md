# UpCloud WHMCS Module

This is the official WHMCS provisioning module for [UpCloud](https://upcloud.com/), allowing you to offer and manage UpCloud VPS services through your WHMCS installation.

## Features

- Automated provisioning of UpCloud VPS servers
- Server management capabilities (start, stop, reboot)
- Client area interface for server management
- Bandwidth monitoring and usage tracking
- Reverse DNS management
- Package/plan upgrades and downgrades
- Support for UpCloud's API

## Requirements

- WHMCS 8.10 or later
- UpCloud API credentials

## Installation

See [INSTALL.md](docs/INSTALL.md) for instructions.

## Client Area Features

The module provides a comprehensive client area interface that allows your clients to:

- View server details and specifications
- Monitor bandwidth usage
- Start, stop, and reboot their VPS
- Manage reverse DNS settings
- View server status and uptime

## Admin Features

As an administrator, you can:

- Provision new VPS servers automatically
- Suspend, unsuspend, and terminate accounts
- Change server packages/plans
- View detailed server information
- Manage reverse DNS settings

## Module Structure

The module follows the standard WHMCS module structure:

```
modules/servers/upCloudVps/
├── upCloudVps.php       # Main module file with all WHMCS hook functions
├── templates/           # Client area templates
│   ├── overview.tpl     # Main client area template
│   ├── error.tpl        # Error template
│   └── assets/          # CSS, JS, and image assets
├── lib/                 # Module libraries and classes
├── cron/                # Cron job scripts for usage updates
└── lang/                # Language files for internationalization
```

## Contributing

Contributions to the UpCloud WHMCS module are welcome. To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit`), prefer commit messages that adhere to [conventional commits](https://www.conventionalcommits.org)
4. Push the branch (`git push -u origin feature/amazing-feature`)
5. Open a Pull Request

## License

This module is released under the MIT License. See the [LICENSE](LICENSE) file for details.