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
 * Web service definitions for local_userfeedback.
 *
 * @package     local_userfeedback
 * @category    webservice
 * @copyright   2025 Marcelo M. Almeida Jr.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$services = [
    'local_userfeedback_services' => [
        'functions' => [
            'local_userfeedback_submit_feedback',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];

$functions = [
    'local_userfeedback_submit_feedback' => [
        'classname'   => \local_userfeedback\external\submit_feedback::class,
        'methodname'  => 'submit',
        'classpath'   => '',
        'description' => 'Recebe feedback enviado via AJAX.',
        'type'        => 'write',
        'ajax'        => true,
    ],
];
