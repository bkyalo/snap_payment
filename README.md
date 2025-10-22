# SNAP Payment Gateway for Moodle

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A secure payment gateway plugin for Moodle that integrates with the SNAP payment processing system.

## Features

- Seamless integration with Moodle's payment subsystem
- Secure payment processing
- Support for multiple payment methods
- Transaction logging and tracking
- Easy configuration through Moodle's admin interface
- Support for multiple currencies

## Requirements

- Moodle 4.0+ (or latest stable version)
- PHP 7.4 or higher
- cURL extension enabled
- OpenSSL extension enabled

## Installation

1. Clone this repository into your Moodle's `payment/gateway/` directory:
   ```
   git clone https://github.com/bkyalo/snap_payment.git /path/to/moodle/payment/gateway/snap
   ```

2. Log in to your Moodle site as an administrator
3. Go to Site administration > Notifications
4. Follow the on-screen instructions to complete the installation
5. Configure the plugin at: Site administration > Plugins > Payment gateways > Manage payment gateways > SNAP

## Configuration

1. Navigate to Site administration > Plugins > Payment gateways > Manage payment gateways
2. Click on "Configure" next to SNAP Payment Gateway
3. Enter your SNAP API credentials:
   - Gateway URL
   - Secret Key
   - (Optional) Custom success URL
4. Save the settings

## Usage

1. Enable the payment gateway in your course settings
2. Configure the payment amount and currency
3. Students will see the SNAP payment option during enrollment
4. After successful payment, they will be automatically enrolled in the course

## Support

For support, please open an issue in the [GitHub repository](https://github.com/bkyalo/snap_payment/issues).

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Author

- **Your Name** - [@bkyalo](https://github.com/bkyalo)
