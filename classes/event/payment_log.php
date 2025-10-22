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
 * The payment_log event class.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_snap\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The payment_log event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string message: The log message.
 *      - string level: The log level (debug, info, warning, error).
 *      - int courseid: The course ID (if applicable).
 *      - string exception: The exception message (if any).
 * }
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class payment_log extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:payment_log', 'paygw_snap');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $description = $this->other['message'];
        
        if (!empty($this->other['courseid'])) {
            $description .= ' (Course ID: ' . $this->other['courseid'] . ')';
        }
        
        if (!empty($this->other['exception'])) {
            $description .= ' - Exception: ' . $this->other['exception'];
        }
        
        return $description;
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        if (!empty($this->other['courseid'])) {
            return new \moodle_url('/course/view.php', ['id' => $this->other['courseid']]);
        }
        return new \moodle_url('/');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        
        if (!isset($this->other['message'])) {
            throw new \coding_exception('The \'message\' value must be set in other.');
        }
        
        if (!isset($this->other['level'])) {
            $this->other['level'] = 'info';
        }
        
        if (!in_array($this->other['level'], ['debug', 'info', 'warning', 'error'])) {
            throw new \coding_exception('The \'level\' value must be one of: debug, info, warning, error');
        }
    }

    /**
     * Get the backup/restore mapping for this event.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        // No mapping required for this event.
        return ['db' => 'paygw_snap', 'restore' => 'paygw_snap'];
    }

    /**
     * Get the other mapping for this event.
     *
     * @return array
     */
    public static function get_other_mapping() {
        // No mapping required for other fields.
        return [];
    }
}
