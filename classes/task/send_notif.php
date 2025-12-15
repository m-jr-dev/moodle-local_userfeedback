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

namespace local_userfeedback\task;

/**
 * Scheduled task responsible for sending user feedback notifications.
 *
 * @package     local_userfeedback
 * @category    task
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_notif extends \core\task\scheduled_task {

    /**
     * Returns the name of the scheduled task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('scheduledtaskname', 'local_userfeedback');
    }

    /**
     * Executes the scheduled task.
     *
     * @return void
     */
    public function execute(): void {
        global $DB;

        $record = new \stdClass();
        $record->component = 'local_userfeedback';
        $record->eventname = 'scheduled_run';
        $record->timecreated = time();

        $DB->insert_record('local_userfeedback_log', $record);
    }
}
