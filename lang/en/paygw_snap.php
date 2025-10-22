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
 * Strings for component 'paygw_snap', language 'en'.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin info
$string['pluginname'] = 'SNAP Payments';
$string['gatewayname'] = 'SNAP Payments';
$string['gatewaydescription'] = 'Securely process payments through the SNAP payment gateway.';

// Configuration form strings
$string['gatewayurl'] = 'Payment Gateway URL';
$string['gatewayurl_help'] = 'The URL of the SNAP payment gateway (e.g., https://pay.self.com)';
$string['secretkey'] = 'Secret Key';
$string['secretkey_help'] = 'The secret key used for encrypting payment data';
$string['successurl'] = 'Success URL';
$string['successurl_help'] = 'The URL where users will be redirected after successful payment';

// Payment status
$string['paymentredirect'] = 'Redirecting to payment gateway...';
$string['paymentredirect_help'] = 'Please wait while we redirect you to the secure payment gateway.';
$string['paymentpending'] = 'Payment pending';
$string['paymentcompleted'] = 'Payment completed';
$string['paymentfailed'] = 'Payment failed';
$string['paymentcancelled'] = 'Payment cancelled';

// Enrollment strings
$string['assignrole'] = 'Assign role';
$string['cost'] = 'Enrol cost';
$string['cost_help'] = 'The cost of enrolling in the course. Leave empty for free enrollment.';
$string['costerror'] = 'The enrollment cost is not numeric';
$string['costorkey'] = 'Please choose one of the following methods of enrollment.';
$string['currency'] = 'Currency';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during SNAP payments enrollment';

// Enrollment dates
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrollment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrollment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrollment is valid (in seconds). If set to zero, the enrollment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrollment is valid, starting with the moment the user is enrolled. If disabled, the enrollment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';

// Expiration and notifications
$string['expiredaction'] = 'Enrollment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrollment expires. Please note that some user data and settings are purged from course during course unenrollment.';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},';
$string['expirymessageenrolledsubject'] = 'Enrollment expiry notification';
$string['expirymessageenrollerbody'] = 'Enrollment in the course \'{$a->course}\'';
$string['expirymessageenrollersubject'] = 'Enrollment expiry notification';

// Notifications
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';

// Enrollment limits
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can SNAP payments enroll. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to SNAP payments-enroll was already reached.';

// Payment messages
$string['messageprovider:expiry_notification'] = 'SNAP Payments enrollment expiry notifications';
$string['messageprovider:enrolment'] = 'SNAP Payments enrollment messages';
$string['newenrols'] = 'Allow new SNAP payments enrollments';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['paymentrequired'] = 'You must make a payment to enroll in this course.';
$string['paymentbutton'] = 'Proceed to Payment';
$string['paymentdescription'] = 'Payment for course: {$a}';
$string['paymentsuccess'] = 'Payment successful. You are now enrolled in the course.';
$string['paymentcancelled'] = 'Payment was cancelled. You have not been enrolled in the course.';

// Error messages
$string['error:invalidpayment'] = 'Invalid payment. Please try again or contact support if the problem persists.';
$string['error:paymentfailed'] = 'Payment failed. Please try again or contact support if the problem persists.';
$string['error:unexpected'] = 'An unexpected error occurred. Please try again or contact support.';

// Plugin description
$string['pluginname_desc'] = 'The SNAP Payments module allows you to set up paid courses. If the cost for any course is zero, then students are not asked to pay for entry. There is a site-wide cost that you set here as a default for the whole site and then a course setting that you can set for each course individually. The course cost overrides the site cost.';

// Enrollment messages
$string['enrolmentnew'] = 'New enrollment in {$a}';
$string['enrolmentnewuser'] = '{$a->user} has enrolled in course "{$a->course}"';
$string['enrolmentconfirmation'] = 'Your enrollment was successful';
$string['enrolmentconfirmation_help'] = 'You have been successfully enrolled in the course. You can now access all course materials.';
$string['enrolmentconfirmation_subject'] = 'Enrollment Confirmation: {$a}';
$string['enrolmentconfirmation_body'] = 'Dear {$a->user},' . "\n\n" . 'You have been successfully enrolled in the course: {$a->course}.' . "\n\n" . 'You can access the course at any time by clicking the following link: {$a->courseurl}.' . "\n\n" . 'Best regards,' . "\n" . '{$a->support}';

// Event strings
$string['event:payment_completed'] = 'Payment completed';
$string['event:payment_failed'] = 'Payment failed';
$string['event:payment_log'] = 'Payment log entry';

// Settings
$string['settings_heading'] = 'SNAP Payments Settings';
$string['loglevel'] = 'Logging Level';
$string['loglevel_help'] = 'Set the level of detail for logs. Higher levels include more detailed information for debugging.';
$string['loglevel:debug'] = 'Debug (most detailed)';
$string['loglevel:info'] = 'Info (recommended)';
$string['loglevel:warning'] = 'Warnings and errors only';
$string['loglevel:error'] = 'Errors only';
$string['testmode'] = 'Test Mode';
$string['testmode_help'] = 'When enabled, payments will be processed in test mode. No real money will be charged.';
$string['error:connectionfailed'] = 'Could not connect to the payment gateway. Please try again later or contact support if the problem persists.';
$string['error:invalidresponse'] = 'Invalid response from payment gateway';
$string['error:decryptionfailed'] = 'Failed to decrypt payment response';
$string['error:transactionnotfound'] = 'Transaction not found';
$string['error:paymentnotverified'] = 'Payment could not be verified';

// Privacy API.
$string['privacy:metadata:paygw_snap:payments'] = 'Stores information about SNAP payments';
$string['privacy:metadata:paygw_snap:payments:userid'] = 'The user ID who made the payment';
$string['privacy:metadata:paygw_snap:payments:amount'] = 'The amount of the payment';
$string['privacy:metadata:paygw_snap:payments:currency'] = 'The currency of the payment';
$string['privacy:metadata:paygw_snap:payments:timecreated'] = 'The time when the payment was initiated';
$string['privacy:metadata:paygw_snap:payments:timemodified'] = 'The time when the payment record was last updated';
$string['privacy:metadata:paygw_snap:payments:status'] = 'The status of the payment';

// Capabilities.
$string['paygw:manageaccount'] = 'Manage SNAP payment account';
$string['paygw:viewpayment'] = 'View SNAP payment information';
