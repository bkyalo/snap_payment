<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * SNAP Payments plugin settings.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create a settings page
    $settings = new admin_settingpage('paygw_snap', new lang_string('pluginname', 'paygw_snap'));
    $ADMIN->add('paymentgateways', $settings);

    // Add settings header
    $settings->add(new admin_setting_heading('paygw_snap_settings', '', 
        get_string('settings_heading', 'paygw_snap')));

    // Gateway URL
    $settings->add(new admin_setting_configtext(
        'paygw_snap/gatewayurl',
        get_string('gatewayurl', 'paygw_snap'),
        get_string('gatewayurl_help', 'paygw_snap'),
        'https://pay.self.com',
        PARAM_URL
    ));

    // Secret Key
    $settings->add(new admin_setting_configpasswordunmask(
        'paygw_snap/secretkey',
        get_string('secretkey', 'paygw_snap'),
        get_string('secretkey_help', 'paygw_snap'),
        ''
    ));

    // Success URL
    global $CFG;
    $settings->add(new admin_setting_configtext(
        'paygw_snap/successurl',
        get_string('successurl', 'paygw_snap'),
        get_string('successurl_help', 'paygw_snap'),
        $CFG->wwwroot . '/payment/gateway/snap/return.php',
        PARAM_URL
    ));

    // Test Mode
    $settings->add(new admin_setting_configcheckbox(
        'paygw_snap/testmode',
        get_string('testmode', 'paygw_snap'),
        get_string('testmode_help', 'paygw_snap'),
        1 // Default to enabled for safety
    ));

    // Logging Level
    $options = [
        'debug' => get_string('loglevel:debug', 'paygw_snap'),
        'info' => get_string('loglevel:info', 'paygw_snap'),
        'warning' => get_string('loglevel:warning', 'paygw_snap'),
        'error' => get_string('loglevel:error', 'paygw_snap'),
    ];
    $settings->add(new admin_setting_configselect(
        'paygw_snap/loglevel',
        get_string('loglevel', 'paygw_snap'),
        get_string('loglevel_help', 'paygw_snap'),
        'info',
        $options
    ));
}
