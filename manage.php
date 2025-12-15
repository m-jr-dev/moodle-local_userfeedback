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
 * Management page for viewing submitted feedback entries.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/formslib.php');

require_login();

$context = context_system::instance();
require_capability('local/userfeedback:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/userfeedback/manage.php');
$PAGE->set_title(get_string('manage', 'local_userfeedback'));
$PAGE->set_heading(get_string('manage', 'local_userfeedback'));

/**
 * Filter form used above the results table.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_userfeedback_filter_form extends moodleform {

    /**
     * Defines filter form elements.
     *
     * @return void
     *
     * @package     local_userfeedback
     * @category    admin
     * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    public function definition() {
        $m = $this->_form;

        $m->addElement('text', 'search', get_string('searchcomment', 'local_userfeedback'));
        $m->setType('search', PARAM_RAW);

        $ratings = [
            '' => get_string('allratings', 'local_userfeedback'),
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];
        $m->addElement('select', 'rating', get_string('rating', 'local_userfeedback'), $ratings);

        $users = get_users_listing('lastname', 'ASC', 0, 999999);
        $uopts = ['' => get_string('allusers', 'local_userfeedback')];
        foreach ($users as $u) {
            $uopts[$u->id] = fullname($u);
        }
        $m->addElement('select', 'userid', get_string('user', 'local_userfeedback'), $uopts);

        $m->addElement('date_selector', 'fromdate', get_string('fromdate', 'local_userfeedback'));
        $m->addElement('date_selector', 'todate', get_string('todate', 'local_userfeedback'));

        $this->add_action_buttons(false, get_string('filter', 'local_userfeedback'));
    }
}

/**
 * Table for displaying feedback entries with CRUD actions.
 *
 * @package     local_userfeedback
 * @category    admin
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_userfeedback_manage_table extends table_sql {

    /**
     * Username column.
     *
     * @param stdClass $record
     * @return string
     *
     * @package     local_userfeedback
     */
    public function col_userid($record) {
        $user = core_user::get_user($record->userid);
        return fullname($user);
    }

    /**
     * Comment column.
     *
     * @param stdClass $record
     * @return string
     *
     * @package     local_userfeedback
     */
    public function col_comment($record) {
        return shorten_text(format_string($record->comment), 80);
    }

    /**
     * Time created column.
     *
     * @param stdClass $record
     * @return string
     *
     * @package     local_userfeedback
     */
    public function col_timecreated($record) {
        return userdate($record->timecreated);
    }

    /**
     * Options column (edit/delete).
     *
     * @param stdClass $record
     * @return string
     *
     * @package     local_userfeedback
     */
    public function col_actions($record) {
        global $OUTPUT;

        $editurl = new moodle_url('/local/userfeedback/edit.php', ['id' => $record->id]);
        $delurl  = new moodle_url('/local/userfeedback/delete.php', ['id' => $record->id]);

        return $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')))
            . ' ' .
               $OUTPUT->action_icon($delurl, new pix_icon('t/delete', get_string('delete')));
    }
}

// Instantiate filter form.
$form = new local_userfeedback_filter_form($PAGE->url);
$filters = $form->get_data();

// Initialise SQL.
$sqlfrom  = "{local_userfeedback} f";
$sqlwhere = "1=1";
$params   = [];

// Apply filters.
if (!empty($filters->search)) {
    global $DB;
    $sqlwhere .= " AND " . $DB->sql_like('f.comment', ':search', false);
    $params['search'] = '%' . $filters->search . '%';
}

if (!empty($filters->rating)) {
    $sqlwhere .= " AND f.rating = :rating";
    $params['rating'] = $filters->rating;
}

if (!empty($filters->userid)) {
    $sqlwhere .= " AND f.userid = :userid";
    $params['userid'] = $filters->userid;
}

if (!empty($filters->fromdate)) {
    $sqlwhere .= " AND f.timecreated >= :fromdate";
    $params['fromdate'] = $filters->fromdate;
}

if (!empty($filters->todate)) {
    $sqlwhere .= " AND f.timecreated <= :todate";
    $params['todate'] = $filters->todate + 86399;
}

// Configure table.
$table = new local_userfeedback_manage_table('local_userfeedback_manage');

$table->define_columns(['id', 'userid', 'rating', 'comment', 'timecreated', 'actions']);
$table->define_headers([
    get_string('id', 'local_userfeedback'),
    get_string('user', 'local_userfeedback'),
    get_string('rating', 'local_userfeedback'),
    get_string('comment', 'local_userfeedback'),
    get_string('time', 'local_userfeedback'),
    get_string('actions', 'local_userfeedback'),
]);

$table->set_attribute('class', 'generaltable generalbox');
$table->set_sql('f.*', $sqlfrom, $sqlwhere, $params);
$table->define_baseurl($PAGE->url);

// Output page.
echo $OUTPUT->header();
$form->display();
$table->out(50, true);
echo $OUTPUT->footer();

