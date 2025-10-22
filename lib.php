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
 * Library of functions for SNAP Payments.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * SNAP Payments plugin supports the following features:
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function paygw_snap_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 * @return void
 */
function paygw_snap_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER, $DB;

    if (!isloggedin() || isguestuser()) {
        return;
    }

    // Add payment history section if user has any payments.
    $context = context_system::instance();
    if (has_capability('paygw/snap:view', $context)) {
        $payments = $DB->get_records('paygw_snap', ['userid' => $user->id], 'timecreated DESC', '*', 0, 10);
        
        if (!empty($payments)) {
            $category = new core_user\output\myprofile\category(
                'paygw_snap', 
                get_string('paymenthistory', 'paygw_snap'), 
                'contact'
            );
            $tree->add_category($category);

            foreach ($payments as $payment) {
                $node = new core_user\output\myprofile\node(
                    'paygw_snap',
                    'payment_' . $payment->id,
                    userdate($payment->timecreated) . ' - ' . 
                    get_string('amount', 'paygw_snap') . ': ' . 
                    format_float($payment->amount, 2) . ' ' . $payment->currency,
                    null,
                    null,
                    null,
                    null,
                    'text-muted'
                );
                $tree->add_node($node);
            }
        }
    }
}

/**
 * Serves the plugin attachments. Implements needed access control.
 *
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function paygw_snap_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB, $USER;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    // Check the filearea is one of the ones we handle.
    if (!in_array($filearea, ['receipts', 'invoices'])) {
        return false;
    }

    require_login();

    // Check the user has the required capabilities.
    if (!has_capability('paygw/snap:view', $context)) {
        return false;
    }

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'paygw_snap', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Callback function that is called when a user completes a payment.
 *
 * @param stdClass $payment Payment record
 * @param string $component The component name
 * @param string $paymentarea The payment area
 * @param int $itemid The item id
 * @return bool True if successful, false otherwise
 */
function paygw_snap_payment_completed($payment, $component, $paymentarea, $itemid) {
    global $DB, $CFG;
    
    // Get the payment record.
    $paymentrecord = $DB->get_record('payments', ['id' => $payment->paymentid], '*', MUST_EXIST);
    
    // Update the payment status.
    $paymentrecord->status = 1; // 1 = completed
    $paymentrecord->timemodified = time();
    $DB->update_record('payments', $paymentrecord);
    
    // Log the successful payment.
    $params = [
        'context' => context_system::instance(),
        'objectid' => $payment->id,
        'relateduserid' => $payment->userid,
        'other' => [
            'paymentid' => $payment->paymentid,
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid
        ]
    ];
    $event = \paygw_snap\event\payment_completed::create($params);
    $event->trigger();
    
    return true;
}
