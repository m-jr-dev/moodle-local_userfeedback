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
 * Edit page for updating a feedback entry.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/form/edit_form.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
require_capability('local/userfeedback:manage', $context);

$PAGE->set_url('/local/userfeedback/edit.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('edit', 'local_userfeedback'));
$PAGE->set_heading(get_string('edit', 'local_userfeedback'));

global $DB;

// Validate feedback entry.
$record = $DB->get_record('local_userfeedback', ['id' => $id], '*', IGNORE_MISSING);
if (!$record) {
    throw new moodle_exception('invalidrecord', 'error');
}

// Initialize form.
$form = new local_userfeedback_edit_form(null, ['id' => $id]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/userfeedback/manage.php'));
}

if ($data = $form->get_data()) {

    $update = new stdClass();
    $update->id = $record->id;
    $update->rating = $data->rating;
    $update->comment = $data->comment;
    $update->timemodified = time();

    $DB->update_record('local_userfeedback', $update);

    redirect(
        new moodle_url('/local/userfeedback/manage.php'),
        get_string('recordupdated', 'local_userfeedback')
    );
}

// Initial form values.
$form->set_data([
    'id' => $record->id,
    'rating' => $record->rating,
    'comment' => $record->comment,
]);

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
