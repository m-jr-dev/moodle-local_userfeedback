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

namespace local_userfeedback\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use context_system;

/**
 * External API handler for submitting feedback.
 *
 * @package     local_userfeedback
 * @category    external
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submit_feedback extends external_api {

    /**
     * Defines the parameters for submit().
     *
     * @return external_function_parameters
     */
    public static function submit_parameters() {
        return new external_function_parameters([
            'rating' => new external_value(PARAM_INT, 'Rating 1-5'),
            'comment' => new external_value(PARAM_TEXT, 'Optional comment', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Processes the feedback submission.
     *
     * @param int $rating Rating value.
     * @param string $comment Optional feedback comment.
     * @return array
     */
    public static function submit($rating, $comment = '') {
        global $DB, $USER;

        $params = self::validate_parameters(
            self::submit_parameters(),
            [
                'rating' => $rating,
                'comment' => $comment,
            ]
        );

        $record = (object) [
            'userid' => $USER->id,
            'rating' => $params['rating'],
            'comment' => $params['comment'],
            'timecreated' => time(),
        ];

        $DB->insert_record('local_userfeedback', $record);

        $event = \local_userfeedback\event\feedback_submitted::create([
            'context' => context_system::instance(),
            'relateduserid' => $USER->id,
        ]);
        $event->trigger();

        return [
            'status' => true,
            'message' => get_string('thankyou', 'local_userfeedback'),
        ];
    }

    /**
     * Defines the structure returned by submit().
     *
     * @return external_single_structure
     */
    public static function submit_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'Operation result'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }
}
