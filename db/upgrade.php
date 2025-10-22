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
 * Upgrade code for the SNAP Payments plugin.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin from an old version to a newer one.
 *
 * @param int $oldversion The old version of the plugin
 * @return bool Result of the upgrade
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_paygw_snap_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024102201) {
        // Define table paygw_snap to be created.
        $table = new xmldb_table('paygw_snap');

        // Adding fields to table paygw_snap.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'user_id');
        $table->add_field('transaction_id', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'course_id');
        $table->add_field('amount_paid', XMLDB_TYPE_NUMBER, '10,2', null, XMLDB_NOTNULL, null, '0.00', 'transaction_id');
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'pending', 'amount_paid');
        $table->add_field('payment_timestamp', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'status');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'payment_timestamp');

        // Adding keys to table paygw_snap.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('user', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id'], 'cascade');
        $table->add_key('course', XMLDB_KEY_FOREIGN, ['course_id'], 'course', ['id'], 'cascade');

        // Adding indexes to table paygw_snap.
        $table->add_index('transactionid', XMLDB_INDEX_UNIQUE, ['transaction_id']);
        $table->add_index('user_course', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'course_id']);
        $table->add_index('status', XMLDB_INDEX_NOTUNIQUE, ['status']);

        // Conditionally launch create table for paygw_snap.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Snap savepoint reached.
        upgrade_plugin_savepoint(true, 2024102201, 'paygw', 'snap');
    }

    return true;
}
