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

namespace paygw_snap;

use core_payment\form\account_gateway;
use core_payment\helper;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/payment/gateway/snap/thirdparty/autoload.php');

/**
 * The gateway class for the SNAP payment plugin.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_payment\gateway {
    
    /**
     * Configuration form for the gateway instance.
     *
     * @param account_gateway $form The form object to add elements to.
     * @param stdClass $config The gateway configuration values.
     * @param string $mform The form element wrapper.
     */
    public static function add_configuration_to_gateway_form(account_gateway $form): void {
        $mform = $form->get_mform();
        $config = $form->get_gateway_configuration();
        // Payment Gateway URL
        $mform->addElement('text', 'gatewayurl', get_string('gatewayurl', 'paygw_snap'), ['size' => '60']);
        $mform->setType('gatewayurl', PARAM_URL);
        $mform->addHelpButton('gatewayurl', 'gatewayurl', 'paygw_snap');
        $mform->addRule('gatewayurl', get_string('required'), 'required', null, 'client');
        $mform->setDefault('gatewayurl', 'https://pay.self.com');

        // Secret Key
        $mform->addElement('passwordunmask', 'secretkey', get_string('secretkey', 'paygw_snap'));
        $mform->setType('secretkey', PARAM_TEXT);
        $mform->addHelpButton('secretkey', 'secretkey', 'paygw_snap');
        $mform->addRule('secretkey', get_string('required'), 'required', null, 'client');
        
        // Success URL (optional)
        $mform->addElement('text', 'successurl', get_string('successurl', 'paygw_snap'), ['size' => '60']);
        $mform->setType('successurl', PARAM_URL);
        $mform->addHelpButton('successurl', 'successurl', 'paygw_snap');
        $mform->setDefault('successurl', $CFG->wwwroot . '/payment/gateway/snap/return.php');
    }

    /**
     * Validate the configuration form elements.
     *
     * @param account_gateway $form The form object to validate.
     * @param stdClass $data The form data.
     * @param stdClass $config The gateway configuration values.
     * @param array $files The uploaded files.
     * @param array $errors The form errors.
     */
    public static function validate_gateway_form(
        account_gateway $form,
        \stdClass $data,
        array $files,
        array &$errors
    ): void {
        $config = $form->get_gateway_configuration();
        // Add any custom validation if needed.
    }

    /**
     * Process payment.
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $itemid
     * @param string $description
     * @param float $amount
     * @param string $currency
     * @param string $returnurl
     * @param string $cancelurl
     * @return string The URL to redirect to for payment.
     */
    public function process_payment(
        string $component,
        string $paymentarea,
        int $itemid,
        string $description,
        float $amount,
        string $currency,
        string $returnurl,
        string $cancelurl
    ): string {
        global $DB, $USER, $CFG;
        
        // Get the course details
        $course = $DB->get_record('course', ['id' => $itemid], 'id,fullname,shortname,idnumber', MUST_EXIST);
        
        // Create a payment record
        $paymentid = helper::save_payment(
            $this->config->accountid,
            $component,
            $paymentarea,
            $itemid,
            $USER->id,
            $amount,
            $currency,
            'paygw_snap'
        );
        
        // Generate a unique transaction ID
        $transactionid = 'moodle_' . time() . '_' . $paymentid;
        
        // Prepare the payload
        $payload = [
            'user_id' => $USER->id,
            'course_id' => $itemid,
            'course_fullname' => $course->fullname,
            'course_shortname' => $course->shortname,
            'amount' => $amount,
            'currency' => $currency,
            'email' => $USER->email,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname,
            'transaction_id' => $transactionid,
            'return_url' => $this->config->successurl ?: $returnurl,
            'cancel_url' => $cancelurl,
            'timestamp' => time()
        ];
        
        // Encrypt the payload using the secret key
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt(
            json_encode($payload),
            'aes-256-cbc',
            $this->config->secretkey,
            0,
            $iv
        );
        
        // Prepare the data to be sent
        $data = [
            'data' => base64_encode($iv . $encrypted)
        ];
        
        // Store the transaction details
        $record = new \stdClass();
        $record->paymentid = $paymentid;
        $record->userid = $USER->id;
        $record->component = $component;
        $record->paymentarea = $paymentarea;
        $record->itemid = $itemid;
        $record->amount = $amount;
        $record->currency = $currency;
        $record->transactionid = $transactionid;
        $record->status = 'pending';
        $record->timecreated = time();
        $record->timemodified = time();
        
        $DB->insert_record('paygw_snap', $record);
        
        // Redirect to the payment gateway with the encrypted data
        $gatewayurl = rtrim($this->config->gatewayurl, '/') . '?' . http_build_query($data);
        
        // Return the URL to redirect to the payment gateway
        return $gatewayurl;
        global $CFG, $DB, $USER;

        // Get the payment account.
        $account = helper::get_payment_account($this->config->accountid);
        
        // Get the payment amount and currency.
        $payable = helper::get_payable($component, $paymentarea, $itemid);
        $amount = $payable->get_amount();
        $currency = $payable->get_currency();
        
        // Create a payment record.
        $paymentid = helper::save_payment(
            $account->get('id'),
            $component,
            $paymentarea,
            $itemid,
            $USER->id,
            $amount,
            $currency,
            'paygw_snap'
        );

        // TODO: Implement SNAP API integration here
        // This is where you would make the API call to SNAP
        
        // For now, we'll just return to the return URL
        return $returnurl;
    }

    /**
     * Handle the return from the payment gateway after payment.
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $itemid
     * @return \core\payment\transaction The payment transaction.
     */
    public function handle_return(
        string $component,
        string $paymentarea,
        int $itemid
    ): \core\payment\transaction {
        global $DB, $USER;
        
        // Get the payment record
        $payment = $DB->get_record('payments', [
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'userid' => $USER->id,
            'paymentmethod' => 'paygw_snap'
        ], '*', MUST_EXIST);
        
        // Get the transaction record
        $transaction = $DB->get_record('paygw_snap', [
            'paymentid' => $payment->id,
            'userid' => $USER->id
        ], '*', MUST_EXIST);
        
        // Check if we have a response from the payment gateway
        $response = optional_param('response', null, PARAM_RAW);
        
        if ($response) {
            // Decrypt the response
            $data = json_decode($response, true);
            
            if ($data && isset($data['status']) && $data['status'] === 'success') {
                // Update the transaction status
                $transaction->status = 'completed';
                $transaction->timemodified = time();
                $DB->update_record('paygw_snap', $transaction);
                
                // Update the payment record
                $payment->status = 1; // Mark as paid
                $payment->timemodified = time();
                $DB->update_record('payments', $payment);
                
                // Return a successful transaction
                $transaction = new \core\payment\transaction($payment);
                $transaction->set_status(\core\payment\transaction::STATUS_COMPLETED);
                return $transaction;
            }
        }
        
        // If we get here, the payment was not successful
        $transaction->status = 'failed';
        $transaction->timemodified = time();
        $DB->update_record('paygw_snap', $transaction);
        
        throw new \moodle_exception('paymentfailed', 'paygw_snap', '', null, 'Payment was not successful');
        global $DB;

        // TODO: Implement return handling
        // Verify the payment with SNAP API
        
        // For now, we'll just mark the payment as completed
        $payment = $DB->get_record('payments', [
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'paymentid' => $this->config->id
        ], '*', MUST_EXIST);

        $transaction = new \core\payment\transaction($payment);
        $transaction->set_status(\core\payment\transaction::STATUS_COMPLETED);
        
        return $transaction;
    }
}
