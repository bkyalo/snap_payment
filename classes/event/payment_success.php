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
 * Event for when a payment is successfully processed.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_snap\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered when a payment is successfully processed.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int paymentid: The ID of the payment.
 *      - float amount: The amount paid.
 *      - string currency: The currency code.
 *      - string transactionid: The transaction ID from the payment gateway.
 * }
 */
class payment_success extends \core\event\base {

    /**
     * Initialize the event.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'paygw_snap';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:payment_success', 'paygw_snap');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '{$this->userid}' has made a successful payment with transaction ID '{$this->other['transactionid']}' " .
               "for the amount of {$this->other['amount']} {$this->other['currency']}.";
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['paymentid'])) {
            throw new \coding_exception('The \'paymentid\' value must be set in other.');
        }
        if (!isset($this->other['amount'])) {
            throw new \coding_exception('The \'amount\' value must be set in other.');
        }
        if (!isset($this->other['currency'])) {
            throw new \coding_exception('The \'currency\' value must be set in other.');
        }
        if (!isset($this->other['transactionid'])) {
            throw new \coding_exception('The \'transactionid\' value must be set in other.');
        }
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/payment/gateway/snap/transactions.php', ['id' => $this->other['paymentid']]);
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the information in a 'customdata' column, even though it is not used.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'paygw_snap', 'restore' => 'payment'];
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the information in a 'customdata' column, even though it is not used.
     *
     * @return array
     */
    public static function get_other_mapping() {
        $othermapped = [];
        $othermapped['paymentid'] = ['db' => 'payments', 'restore' => 'payment'];

        return $othermapped;
    }
}
