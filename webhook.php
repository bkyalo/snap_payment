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
 * Webhook handler for SNAP payment gateway callbacks.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output.
define('NO_DEBUG_DISPLAY', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/completionlib.php');

// Get the raw POST data
$payload = file_get_contents('php://input');

// Log the incoming webhook
\paygw_snap\util::log("Webhook received: " . $payload);

try {
    if (empty($payload)) {
        throw new \moodle_exception('error:emptywebhook', 'paygw_snap');
    }

    // Decode the JSON payload
    $data = json_decode($payload, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \moodle_exception('error:invalidjson', 'paygw_snap', '', json_last_error_msg());
    }

    // Verify required fields
    $required = ['transaction_id', 'status', 'amount', 'currency'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new \moodle_exception('error:missingfield', 'paygw_snap', '', $field);
        }
    }

    // Initialize the payment gateway
    $gateway = new \paygw_snap\gateway();
    
    // Process the webhook
    $result = $gateway->process_webhook($data);
    
    // Log successful processing
    \paygw_snap\util::log("Webhook processed successfully for transaction: " . $data['transaction_id']);
    
    // Return success response
    http_response_code(200);
    echo json_encode(['status' => 'success']);
    
} catch (\moodle_exception $e) {
    // Log the error
    \paygw_snap\util::log("Webhook error: " . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'errorcode' => $e->errorcode ?? ''
    ]);
    
} catch (\Exception $e) {
    // Log unexpected errors
    \paygw_snap\util::log("Unexpected error in webhook: " . $e->getMessage());
    
    // Return generic error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'errorcode' => 'internalerror'
    ]);
}
