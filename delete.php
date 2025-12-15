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

/**
 * Delete page for feedback entries.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/userfeedback:manage', $context);

$id      = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_context($context);
$PAGE->set_url('/local/userfeedback/delete.php', ['id' => $id]);
$PAGE->set_title(get_string('delete', 'local_userfeedback'));
$PAGE->set_heading(get_string('delete', 'local_userfeedback'));

global $DB;

// Retrieve record to confirm existence.
$record = $DB->get_record('local_userfeedback', ['id' => $id], '*', MUST_EXIST);

if ($confirm && confirm_sesskey()) {

    // Delete record.
    $DB->delete_records('local_userfeedback', ['id' => $id]);

    // Redirect to manage page.
    redirect(
        new moodle_url('/local/userfeedback/manage.php'),
        get_string('deleted', 'local_userfeedback'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

// Confirmation box.
$message = get_string('deleteconfirm', 'local_userfeedback');
$yesurl  = new moodle_url('/local/userfeedback/delete.php', [
    'id' => $id,
    'confirm' => 1,
    'sesskey' => sesskey(),
]);
$nourl   = new moodle_url('/local/userfeedback/manage.php');

echo $OUTPUT->confirm($message, $yesurl, $nourl);

echo $OUTPUT->footer();
