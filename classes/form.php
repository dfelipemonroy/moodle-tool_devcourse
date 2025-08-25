<?php
// This file is part of Moodle - http://moodle.org/
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
 * Class form.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_devcourse;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class form for displaying an editing form.
 *
 * @package    tool_devcourse
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form extends \moodleform {

    /**
     * The name of the plugin used within the tool_devcourse component.
     *
     * @var string $pluginname The internal identifier for the devcourse admin tool plugin.
     */
    protected $pluginname = 'tool_devcourse';

    /**
     * Defines the form elements and structure.
     *
     * This method is called to add form fields, validation rules,
     * and other configuration for the form.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name',
            get_string('name', $this->pluginname));
        $mform->setType('name', PARAM_NOTAGS);

        $mform->addElement('advcheckbox', 'completed',
            get_string('completed', $this->pluginname));

        $mform->addElement(
            'editor',
            'description_editor',
            get_string('description', $this->pluginname)
        );

        $this->add_action_buttons();
    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     *
     * @return array
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        // Check that name is unique for the course.
        if ($DB->record_exists_select($this->pluginname,
                'name = :name AND id <> :id AND courseid = :courseid',
                ['name' => $data['name'], 'id' => $data['id'], 'courseid' => $data['courseid']])) {
            $errors['name'] = get_string('errornameexists', $this->pluginname);
        }

        return $errors;
    }
}
