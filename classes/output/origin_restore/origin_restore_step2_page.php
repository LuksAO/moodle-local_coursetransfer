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

namespace local_coursetransfer\output\origin_restore;

use local_coursetransfer\api\request;
use local_coursetransfer\coursetransfer;
use moodle_exception;
use moodle_url;
use renderer_base;
use stdClass;

/**
 * origin_restore_step2_page
 *
 * @package    local_coursetransfer
 * @copyright  2023 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     3IPUNT <contacte@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class origin_restore_step2_page extends origin_restore_step_page {

    /**
     * Export for Template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $USER;
        $data = new stdClass();
        $data->button = true;
        $data->steps = self::get_steps(2);
        $backurl = new moodle_url(self::URL);
        $nexturl = new moodle_url(self::URL,
            ['step' => 3, 'site' => $this->site, 'type' => 'courses']
        );
        $tableurl = new moodle_url(self::URL);
        $data->table_url = $tableurl->out(false);
        $data->back_url = $backurl->out(false);
        $data->next_url = $nexturl->out(false);
        $data->next_url_disabled = true;
        $site = coursetransfer::get_site_by_position($this->site);
        $context = \context_system::instance();
        if (has_capability('local/coursetransfer:origin_view_courses', $context)) {
            try {
                $request = new request($site);
                $res = $request->origin_get_courses($USER);
                if ($res->success) {
                    $courses = $res->data;
                    $datacourses = [];
                    $coursesdest = coursetransfer::get_courses_user($USER);
                    $destinies = [];
                    if (coursetransfer::can_restore_in_not_new_course($USER)) {
                        foreach ($coursesdest as $cd) {
                            $destinies[] = [
                                    'id' => $cd->id,
                                    'name' => $cd->fullname,
                                    'shortname' => $cd->shortname
                            ];
                        }
                    }
                    foreach ($courses as $c) {
                        $c->destinies = $destinies;
                        $datacourses[] = $c;
                    }
                    $data->courses = $datacourses;
                    $data->haserrors = false;
                } else {
                    $data->errors = $res->errors;
                    $data->haserrors = true;
                }
            } catch (moodle_exception $e) {
                $data->errors = ['code' => '20002', 'msg' => $e->getMessage()];
                $data->haserrors = true;
            }
        } else {
            $data->errors = [
                    'code' => '20001',
                    'msg' => get_string('you_have_not_permission', 'local_coursetransfer')];
            $data->haserrors = true;
        }
        return $data;
    }
}
