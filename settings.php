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
 * Settings for local_userfeedback plugin.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add('localplugins',
        new admin_category('local_userfeedback_cat',
            get_string('pluginname', 'local_userfeedback')
        )
    );

    // Register the page.
    $ADMIN->add('local_userfeedback_cat', $settings);

    // Link to manage page.
    $ADMIN->add('local_userfeedback_cat',
        new admin_externalpage(
            'local_userfeedback_manage',
            get_string('manage', 'local_userfeedback'),
            new moodle_url('/local/userfeedback/manage.php'),
            'local/userfeedback:manage'
        )
    );

    // Link to reports page.
    $ADMIN->add('local_userfeedback_cat',
        new admin_externalpage(
            'local_userfeedback_reports',
            get_string('reports', 'local_userfeedback'),
            new moodle_url('/local/userfeedback/reports.php'),
            'local/userfeedback:viewreports'
        )
    );
}
