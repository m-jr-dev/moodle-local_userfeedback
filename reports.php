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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Report page for feedback statistics.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\chart_bar;
use core\chart_pie;
use core\chart_series;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/tablelib.php');

require_login();

$context = context_system::instance();
require_capability('local/userfeedback:viewreports', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/userfeedback/reports.php'));
$PAGE->set_title(get_string('reports', 'local_userfeedback'));
$PAGE->set_heading(get_string('reports', 'local_userfeedback'));

// Download parameter.
$download = optional_param('download', '', PARAM_ALPHA);

/**
 * Filter form used in the report page.
 */
class local_userfeedback_report_filter_form extends moodleform {

    /**
     * Defines filter form fields.
     *
     * @return void
     */
    public function definition() {
        $m = $this->_form;

        $m->addElement('date_selector', 'fromdate', get_string('fromdate', 'local_userfeedback'));
        $m->addElement('date_selector', 'todate', get_string('todate', 'local_userfeedback'));

        $ratings = [
            '' => get_string('allratings', 'local_userfeedback'),
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];

        $m->addElement('select', 'rating', get_string('rating', 'local_userfeedback'), $ratings);
        $this->add_action_buttons(false, get_string('filter', 'local_userfeedback'));
    }
}

$form = new local_userfeedback_report_filter_form($PAGE->url);
$filters = $form->get_data();

global $DB;

// SQL construction.
$sqlwhere = "WHERE 1=1";
$params = [];

if (!empty($filters->fromdate)) {
    $sqlwhere .= " AND timecreated >= :fromdate";
    $params['fromdate'] = $filters->fromdate;
}
if (!empty($filters->todate)) {
    $sqlwhere .= " AND timecreated <= :todate";
    $params['todate'] = $filters->todate + 86399;
}
if (!empty($filters->rating)) {
    $sqlwhere .= " AND rating = :rating";
    $params['rating'] = $filters->rating;
}

$total = $DB->count_records_select('local_userfeedback', str_replace('WHERE', '', $sqlwhere), $params);
$avg = $DB->get_field_sql("SELECT AVG(rating) FROM {local_userfeedback} $sqlwhere", $params);

/**
 * Report table with download support.
 */
class local_userfeedback_report_table extends table_sql {

    /**
     * Returns formatted user name.
     *
     * @param stdClass $record Feedback record.
     * @return string
     */
    public function col_userid($record) {
        $user = core_user::get_user($record->userid);
        return fullname($user);
    }

    /**
     * Returns shortened comment text.
     *
     * @param stdClass $record Feedback record.
     * @return string
     */
    public function col_comment($record) {
        return shorten_text(format_string($record->comment), 80);
    }

    /**
     * Returns formatted timestamp.
     *
     * @param stdClass $record Feedback record.
     * @return string
     */
    public function col_timecreated($record) {
        return userdate($record->timecreated);
    }
}

$table = new local_userfeedback_report_table('local_userfeedback_report');

// Enable Moodle native download handling.
$table->is_downloading($download, 'feedback_report');

// Table definition.
$table->define_columns(['userid', 'rating', 'comment', 'timecreated']);
$table->define_headers([
    get_string('user', 'local_userfeedback'),
    get_string('rating', 'local_userfeedback'),
    get_string('comment', 'local_userfeedback'),
    get_string('time', 'local_userfeedback'),
]);

$table->set_sql(
    'userid, rating, comment, timecreated',
    "{local_userfeedback}",
    str_replace('WHERE ', '', $sqlwhere),
    $params
);
$table->define_baseurl($PAGE->url);

// If downloading, output only data.
if ($table->is_downloading()) {
    $table->out(5000, false);
    exit;
}

echo $OUTPUT->header();

// Summary section.
echo html_writer::div(
    get_string('totalfeedbacks', 'local_userfeedback') . ': ' . $total . '<br>' .
    get_string('averagerating', 'local_userfeedback') . ': ' . round((float)$avg, 2),
    'feedback-dashboard'
);

// Filter form.
$form->display();

// Table.
$table->out(30, true);

// Charts.
echo $OUTPUT->heading("Gráficos", 3);

// Rating aggregation.
$ratingscount = $DB->get_records_sql_menu("
    SELECT rating, COUNT(*)
    FROM {local_userfeedback}
    $sqlwhere
    GROUP BY rating
    ORDER BY rating ASC
", $params);

// Bar chart.
$chart = new chart_bar();
$chart->set_title("Distribuição de Avaliações");
$chart->add_series(new chart_series('Avaliações', array_values($ratingscount)));
$chart->set_labels(array_keys($ratingscount));
echo $OUTPUT->render($chart);

// Pie chart.
$pie = new chart_pie();
$pie->set_title("Percentual por Nota");
$pie->add_series(new chart_series('Notas', array_values($ratingscount)));
$pie->set_labels(array_keys($ratingscount));
echo $OUTPUT->render($pie);

echo $OUTPUT->footer();
