<?php
// This file is part of Moodle - http://moodle.org/.
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

namespace local_userfeedback\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider.
 *
 * @package     local_userfeedback
 * @category    privacy
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Metadata for stored data.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_database_table(
            'local_userfeedback',
            [
                'userid'       => 'privacy:metadata:userid',
                'rating'       => 'privacy:metadata:rating',
                'comment'      => 'privacy:metadata:comment',
                'timecreated'  => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified',
            ],
            'privacy:metadata:table'
        );

        $items->add_database_table(
            'local_userfeedback_log',
            [
                'component'   => 'privacy:metadata:component',
                'eventname'   => 'privacy:metadata:eventname',
                'timecreated' => 'privacy:metadata:timecreated',
            ],
            'privacy:metadata:logtable'
        );

        return $items;
    }

    /**
     * Contexts containing user data.
     */
    public static function get_contexts_for_userid(int $userid): approved_contextlist {
        global $DB;

        $contextlist = new approved_contextlist();

        if ($DB->record_exists('local_userfeedback', ['userid' => $userid])) {
            $contextlist->add_context(\context_system::instance());
        }

        return $contextlist;
    }

    /**
     * Exports user data.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $context = \context_system::instance();
        if (!in_array($context->id, $contextlist->get_contextids())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $records = $DB->get_records('local_userfeedback', ['userid' => $userid]);

        if ($records) {
            writer::with_context($context)->export_data(
                ['local_userfeedback'],
                (object)['records' => array_values($records)]
            );
        }
    }

    /**
     * Users found in this context.
     */
    public static function get_users_in_context(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $users = $DB->get_fieldset_sql("SELECT userid FROM {local_userfeedback}");
        $userlist->add_users($users);
    }

    /**
     * Deletes data for a specific user.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        $DB->delete_records('local_userfeedback', ['userid' => $userid]);
    }

    /**
     * Deletes all data in this context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel === CONTEXT_SYSTEM) {
            $DB->delete_records('local_userfeedback');
        }
    }
}
