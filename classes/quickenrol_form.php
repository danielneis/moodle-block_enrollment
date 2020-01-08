<?php
// This file is part of the tool_certificate for Moodle - http://moodle.org/
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
 * This files contains the form for enrol users in multiple courses
 *
 * @package    block_enrollment
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_enrollment;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/enrol/manual/locallib.php');

/**
 * The form for enrol users in multiple courses
 *
 * @package    block_enrollment
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quickenrol_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $PAGE;

        $mform =& $this->_form;

        $courses = enrol_get_my_courses();
        $course = reset($courses);
        $context = \context_course::instance($course->id);

        $manager = new \course_enrolment_manager($PAGE, $course);
        $instance = null;
        foreach ($manager->get_enrolment_instances() as $tempinstance) {
            if ($tempinstance->enrol == 'manual') {
                if ($instance === null) {
                    $instance = $tempinstance;
                    break;
                }
            }
        }

        $mform->addElement('header', 'main', get_string('enrolmentoptions', 'enrol'));
        $options = array(
            'ajax' => 'enrol_manual/form-potential-user-selector',
            'multiple' => false,
            'courseid' => $course->id,
            'enrolid' => $instance->id,
            'userfields' => implode(',', get_extra_user_fields($context))
        );
        $mform->addElement('autocomplete', 'userid', get_string('selectusers', 'enrol_manual'), array(), $options);
        $mform->addRule('userid', null, 'required', null, 'client');

        $mform->addElement('course', 'courses', get_string('courses', 'block_enrollment'), ['multiple' => true]);
        $mform->addRule('courses', null, 'required', null, 'client');

        $roles = get_assignable_roles($context);
        $mform->addElement('select', 'roletoassign', get_string('assignrole', 'enrol_manual'), $roles);

        $mform->addElement('date_time_selector', 'datestart', get_string('startdate', 'block_enrollment'), ['optional' => true]);
        $mform->addElement('date_time_selector', 'dateend', get_string('enddate', 'block_enrollment'), ['optional' => true]);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
       $errors = [];
       if (!empty($data['datestart']) && !empty($data['dateend']) && ($data['datestart'] >= $data['dateend'])) {
           $errors['datestart'] = get_string('wrongdatestart', 'block_enrollment');
       }
       if (empty($data['userid'])) {
           $errors['userid'] = get_string('emptyuserid', 'block_enrollment');
       }
       return $errors;
    }
}
