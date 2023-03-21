<?php
// This file is part of the local_tresipuntintegrations plugin for Moodle - http://moodle.org/
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
 * Class new_origin_restore_course_step1_form
 *
 * @package    local_coursetransfer
 * @copyright  2023 3iPunt {@link https://tresipunt.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_coursetransfer\forms;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use coding_exception;
use local_coursetransfer\coursetransfer;
use moodle_exception;
use moodleform;
use stdClass;

require_once($CFG->libdir . '/formslib.php');

class new_origin_restore_course_step1_form extends moodleform {

    /**
     * course_form constructor.
     *
     * @param stdClass $course
     * @param string|null $action
     */
    public function __construct(stdClass $course, string $action = null) {
        $this->course = $course;
        parent::__construct($action);
    }

    /**
     * Definition
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function definition() {

        $mform = $this->_form;

        $sites = coursetransfer::get_origin_sites();

        $mform->addElement(
                'select', 'origin_site',
                get_string('origin_site', 'local_coursetransfer'), $sites);
        $mform->addHelpButton('origin_site', 'origin_site', 'local_coursetransfer');

        $this->add_action_buttons(false, get_string('next'));

    }

}
