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
 * Form used for editing feedback entries.
 *
 * @package     local_userfeedback
 * @category    admin
 * @copyright   2025 Marcelo M.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Feedback edit form class.
 */
class local_userfeedback_edit_form extends moodleform {

    /**
     * Defines form fields.
     */
    public function definition() {
        $m = $this->_form;

        $m->addElement('hidden', 'id');
        $m->setType('id', PARAM_INT);

        $ratings = [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
        ];
        $m->addElement('select', 'rating', get_string('rating', 'local_userfeedback'), $ratings);
        $m->setType('rating', PARAM_INT);
        $m->addRule('rating', null, 'required', null, 'client');

        $m->addElement(
            'textarea',
            'comment',
            get_string('comment', 'local_userfeedback'),
            ['rows' => 5, 'cols' => 60]
        );
        $m->setType('comment', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Validates submitted form data.
     *
     * @param array $data
     * @param array $files
     * @return array validation errors
     */
    public function validation($data, $files) {
        $errors = [];

        if ($data['rating'] < 1 || $data['rating'] > 5) {
            $errors['rating'] = get_string('invalidrating', 'local_userfeedback');
        }

        return $errors;
    }
}
