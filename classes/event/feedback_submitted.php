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

namespace local_userfeedback\event;

/**
 * Event triggered when a user submits feedback.
 *
 * @package     local_userfeedback
 * @category    event
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_submitted extends \core\event\base {

    /**
     * Initialise event data.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c'; // Create action.
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'local_userfeedback';
    }

    /**
     * Returns the localized event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('pluginname', 'local_userfeedback') . ': feedback submitted';
    }

    /**
     * Returns event description.
     *
     * @return string
     */
    public function get_description() {
        return 'Feedback submitted by user id ' . $this->relateduserid . '.';
    }
}
