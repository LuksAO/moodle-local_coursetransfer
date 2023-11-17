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

// Project implemented by the "Recovery, Transformation and Resilience Plan.
// Funded by the European Union - Next GenerationEU".
//
// Produced by the UNIMOODLE University Group: Universities of
// Valladolid, Complutense de Madrid, UPV/EHU, León, Salamanca,
// Illes Balears, Valencia, Rey Juan Carlos, La Laguna, Zaragoza, Málaga,
// Córdoba, Extremadura, Vigo, Las Palmas de Gran Canaria y Burgos.

/**
 *
 * @package    local_coursetransfer
 * @copyright  2023 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_coursetransfer\output\origin_restore_course\new_origin_restore_course_step1_page;
use local_coursetransfer\output\origin_restore_course\new_origin_restore_course_step2_page;
use local_coursetransfer\output\origin_restore_course\new_origin_restore_course_step3_page;
use local_coursetransfer\output\origin_restore_course\new_origin_restore_course_step4_page;
use local_coursetransfer\output\origin_restore_course\new_origin_restore_course_step5_page;
use local_coursetransfer\output\origin_restore_course\origin_restore_course_page;

require_once('../../config.php');

global $PAGE, $OUTPUT, $USER;

$courseid = required_param('id', PARAM_INT);
$isnew = optional_param('new', false, PARAM_INT);
$isnew = $isnew === 1;

$title = get_string('origin_restore_course', 'local_coursetransfer');

$course = get_course($courseid);
require_login($course);
$context = context_course::instance($courseid);
$PAGE->set_pagelayout('incourse');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url('/local/coursetransfer/origin_restore_course.php');

$output = $PAGE->get_renderer('local_coursetransfer');

echo $OUTPUT->header();
if (has_capability('local/coursetransfer:origin_restore_course', $context)) {
    if ($isnew) {
        $step = required_param('step', PARAM_INT);
        switch ($step) {
            case 1:
                $page = new new_origin_restore_course_step1_page($course);
                break;
            case 2:
                $page = new new_origin_restore_course_step2_page($course);
                break;
            case 3:
                $page = new new_origin_restore_course_step3_page($course);
                break;
            case 4:
                $page = new new_origin_restore_course_step4_page($course);
                break;
            case 5:
                $page = new new_origin_restore_course_step5_page($course);
                break;
            default:
                throw new moodle_exception('STEP NOT VALID');
        }
    } else {
        $page = new origin_restore_course_page($course);
    }
} else {
    $page = new \local_coursetransfer\output\error_page(
            get_string('forbidden', 'local_coursetransfer'),
            get_string('you_have_not_permission', 'local_coursetransfer'),
            'danger',
            get_string('error')
    );
}

\local_coursetransfer\coursetransfer::get_backup_size_estimated(457);

echo $output->render($page);
echo $OUTPUT->footer();
