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
 * The payment_completed event class.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_snap\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The payment_completed event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int paymentid: The payment ID
 *      - string component: The component name
 *      - string paymentarea: The payment area
 *      - int itemid: The item ID
 * }
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class payment_completed extends \core\event\payment_completed {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'paygw_snap';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:payment_completed', 'paygw_snap');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '{$this->userid}' completed payment with id '{$this->other['paymentid']}'";
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
        if (!isset($this->other['component'])) {
            throw new \coding_exception('The \'component\' value must be set in other.');
        }
        if (!isset($this->other['paymentarea'])) {
            throw new \coding_exception('The \'paymentarea\' value must be set in other.');
        }
        if (!isset($this->other['itemid'])) {
            throw new \coding_exception('The \'itemid\' value must be set in other.');
        }
    }

    /**
     * Get the backup/restore mapping for this event.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'paygw_snap', 'restore' => 'paygw_snap'];
    }
}
