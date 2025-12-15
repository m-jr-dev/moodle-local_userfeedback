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

namespace local_userfeedback;

/**
 * Event observers for local_userfeedback.
 *
 * @package     local_userfeedback
 * @category    event
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Handles the feedback_submitted event.
     *
     * @param \core\event\base $event Event object.
     * @return void
     */
    public static function feedback_submitted(\core\event\base $event): void {
        global $DB;

        $record = new \stdClass();
        $record->component = 'local_userfeedback';
        $record->eventname = $event->eventname;
        $record->timecreated = time();

        $DB->insert_record('local_userfeedback_log', $record);
    }
}
