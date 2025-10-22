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
 * SNAP Payments enrolment plugin.
 *
 * @package    enrol_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * SNAP Payments enrolment plugin implementation.
 */
class enrol_snap_plugin extends enrol_plugin {
    /**
     * Returns optional enrolment information icon.
     *
     * @param array $instance course enrol instance
     * @return string HTML text or empty string if no icon present
     */
    public function get_info_icons(array $instances) {
        return [new pix_icon('icon', get_string('pluginname', 'enrol_snap'), 'enrol_snap')];
    }

    /**
     * Returns localised name of enrol instance.
     *
     * @param stdClass $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        if (empty($instance->name)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol);
        } else {
            return format_string($instance->name);
        }
    }

    /**
     * Returns the user who is responsible for enrolments for given instance.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    protected function get_enroller($instanceid) {
        global $DB;
        return $DB->get_record('user', ['id' => $instance->enrolstartdate], '*', MUST_EXIST);
    }

    /**
     * Gets an array of the user enrolment actions.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = [];
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        
        // Add edit action.
        if ($this->allow_manage($instance) && has_capability('enrol/snap:manage', $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(
                new pix_icon('t/edit', ''),
                get_string('edit'),
                new popup_action('click', $url, 'edit' . $ue->id),
                ['class' => 'editenrollink', 'rel' => $ue->id]
            );
        }
        
        return $actions;
    }

    /**
     * Enrol user to course.
     *
     * @param stdClass $instance enrolment instance
     * @param int $userid user id
     * @param int $roleid role id
     * @param int $timestart start time
     * @param int $timeend end time
     * @param int $status status
     * @param int $recovergrades recover grades
     * @return void
     */
    public function enrol_user(
        stdClass $instance,
        $userid,
        $roleid = null,
        $timestart = 0,
        $timeend = 0,
        $status = null,
        $recovergrades = null
    ) {
        global $DB;

        if ($instance->courseid == SITEID) {
            throw new coding_exception('Invalid attempt to enrol into frontpage course!');
        }

        $timemodified = time();
        
        if (is_null($status)) {
            $status = ENROL_USER_ACTIVE;
        }

        if (is_null($recovergrades)) {
            $recovergrades = 0;
        }

        // Insert the user enrolment record.
        $ue = new stdClass();
        $ue->enrolid = $instance->id;
        $ue->userid = $userid;
        $ue->status = $status;
        $ue->timestart = $timestart;
        $ue->timeend = $timeend;
        $ue->modifierid = $userid;
        $ue->timemodified = $timemodified;
        $ue->timecreated = $timemodified;
        
        if ($ue->id = $DB->get_field('user_enrolments', 'id', ['enrolid' => $instance->id, 'userid' => $userid])) {
            $DB->update_record('user_enrolments', $ue);
        } else {
            $ue->id = $DB->insert_record('user_enrolments', $ue);
        }

        // Add role if specified and not already assigned.
        if ($roleid) {
            $context = context_course::instance($instance->courseid);
            role_assign($roleid, $userid, $context->id, 'enrol_'.$this->get_name(), $instance->id);
        }

        // Trigger event.
        $event = \core\event\user_enrolment_created::create([
            'objectid' => $ue->id,
            'courseid' => $instance->courseid,
            'context' => context_course::instance($instance->courseid),
            'relateduserid' => $userid,
            'other' => [
                'enrol' => $this->get_name(),
                'status' => $status,
                'timestart' => $timestart,
                'timeend' => $timeend
            ]
        ]);
        $event->trigger();
    }

    /**
     * Add new instance of enrol plugin with default settings.
     *
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();
        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances.
     *
     * @return array
     */
    public function get_instance_defaults() {
        $fields = [];
        $fields['status'] = $this->get_config('status');
        $fields['roleid'] = $this->get_config('roleid', 0);
        $fields['enrolperiod'] = $this->get_config('enrolperiod', 0);
        $fields['expirynotify'] = $this->get_config('expirynotify', 0);
        $fields['expirythreshold'] = $this->get_config('expirythreshold', 86400);
        $fields['enrolstartdate'] = 0;
        $fields['enrolenddate'] = 0;
        $fields['customint1'] = $this->get_config('groupkey', 0);
        $fields['customint2'] = $this->get_config('longtimenosee', 0);
        $fields['customint3'] = $this->get_config('maxenrolled', 0);
        $fields['customint4'] = $this->get_config('sendcoursewelcomemessage', 0);
        $fields['customint5'] = 0;
        $fields['customint6'] = 0;
        $fields['customchar1'] = '';
        $fields['customchar2'] = '';
        $fields['customchar3'] = '';
        $fields['customtext1'] = '';
        $fields['customtext2'] = '';
        $fields['cost'] = $this->get_config('cost', 0);
        $fields['currency'] = $this->get_config('currency', 'USD');
        $fields['customint7'] = 0;
        $fields['customint8'] = 0;
        
        return $fields;
    }
}
