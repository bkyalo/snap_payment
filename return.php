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
 * Handles the return from the SNAP payment gateway.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output.
define('NO_DEBUG_DISPLAY', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/completionlib.php');

// Require login and session.
require_login(null, false);
require_sesskey();

// Get and validate required parameters.
$component = required_param('component', PARAM_COMPONENT);
$paymentarea = required_param('paymentarea', PARAM_AREA);
$itemid = required_param('itemid', PARAM_INT);
$response = required_param('response', PARAM_RAW);

try {
    // Log the incoming request for debugging.
    \paygw_snap\util::log("Processing return for component: {$component}, area: {$paymentarea}, item: {$itemid}");
    
    // Initialize the payment gateway.
    $gateway = new \paygw_snap\gateway();
    
    // Process the return and get the transaction.
    $transaction = $gateway->handle_return($component, $paymentarea, $itemid);
    
    // Get the course ID from the payment area.
    $courseid = $transaction->get_itemid();
    $course = get_course($courseid);
    
    // Log successful payment processing.
    \paygw_snap\util::log("Payment processed successfully for course {$courseid}", $courseid);
    
    // Redirect to the course page with success message.
    redirect(
        new moodle_url('/course/view.php', ['id' => $courseid]),
        get_string('paymentcompleted', 'paygw_snap'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
    
} catch (\moodle_exception $e) {
    // Log the detailed error.
    \paygw_snap\util::log("Payment return error: " . $e->getMessage(), $courseid ?? 0, $e);
    
    // Show a generic error message to the user.
    $errormsg = get_string('paymentfailed', 'paygw_snap');
    $returnurl = $courseid ? new moodle_url('/course/view.php', ['id' => $courseid]) : new moodle_url('/');
    
    \core\notification::error($errormsg);
    redirect($returnurl, $errormsg, null, \core\output\notification::NOTIFY_ERROR);
    
} catch (\Exception $e) {
    // Catch any other exceptions.
    \paygw_snap\util::log("Unexpected error in return handler: " . $e->getMessage(), $courseid ?? 0, $e);
    
    $errormsg = get_string('error:unexpected', 'paygw_snap');
    \core\notification::error($errormsg);
    redirect(new moodle_url('/'), $errormsg, null, \core\output\notification::NOTIFY_ERROR);
}
