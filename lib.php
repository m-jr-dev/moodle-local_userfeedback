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
 * Library callbacks for the local_userfeedback plugin.
 *
 * @package     local_userfeedback
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Loads the plugin CSS into every Moodle page.
 *
 * @return void
 */
function local_userfeedback_before_standard_html_head(): void {
    global $PAGE;

    $PAGE->requires->css('/local/userfeedback/styles.css');
}

/**
 * Injects the feedback widget into page footer when applicable.
 *
 * @return void
 */
function local_userfeedback_before_footer(): void {
    global $PAGE, $USER, $DB;

    // Skip if the user is not logged in or is a guest.
    if (!isloggedin() || isguestuser()) {
        return;
    }

    $context = context_system::instance();

    // Requires permission to submit feedback.
    if (!has_capability('local/userfeedback:submit', $context)) {
        return;
    }

    // Hide widget in activity-level pages.
    $cmid = optional_param('id', 0, PARAM_INT);
    if ($cmid > 0) {
        return;
    }

    // Do not show if the user already submitted a feedback.
    if ($DB->record_exists('local_userfeedback', ['userid' => $USER->id])) {
        return;
    }

    // Load AMD module.
    $PAGE->requires->js_call_amd('local_userfeedback/submit', 'init');

    echo '<div id="local-userfeedback-root"></div>';
}

/**
 * Adds an admin navigation link for managing feedback.
 *
 * @param global_navigation $nav The global navigation instance.
 * @return void
 */
function local_userfeedback_extend_navigation(global_navigation $nav): void {
    $context = context_system::instance();

    // Only managers can see this entry.
    if (!has_capability('local/userfeedback:manage', $context)) {
        return;
    }

    $node = $nav->add(get_string('pluginname', 'local_userfeedback'));
    $node->add(
        get_string('manage', 'local_userfeedback'),
        new moodle_url('/local/userfeedback/manage.php')
    );
}

/**
 * Adds a button to the secondary navigation inside a course.
 *
 * @param navigation_node $navigation The course secondary navigation node.
 * @param stdClass $course The course object.
 * @param context_course $context The course context.
 * @return void
 */
function local_userfeedback_extend_course_navigation(navigation_node $navigation, stdClass $course, context_course $context): void {

    // Only managers can see this link.
    if (!has_capability('local/userfeedback:manage', $context)) {
        return;
    }

    // Add a link in the course secondary navigation.
    $navigation->add(
        get_string('manage', 'local_userfeedback'),
        new moodle_url('/local/userfeedback/manage.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_userfeedback_manage'
    );
}

