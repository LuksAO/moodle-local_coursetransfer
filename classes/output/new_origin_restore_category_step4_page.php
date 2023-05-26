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
 * Local Course Transfer
 *
 * @package    local_coursetransfer
 * @copyright  2023 3iPunt {@link https://tresipunt.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursetransfer\output;

use local_coursetransfer\api\request;
use local_coursetransfer\coursetransfer;
use moodle_exception;
use moodle_url;
use renderer_base;
use stdClass;

/**
 * new_origin_restore_category_step4_page
 *
 * @package    local_coursetransfer
 * @copyright  2023 3iPunt {@link https://tresipunt.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class new_origin_restore_category_step4_page extends new_origin_restore_category_step_page {

    /**
     * Export for Template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $siteposition = required_param('site', PARAM_RAW);
        $restoreid = required_param('restoreid', PARAM_INT);
        $destinyid = required_param('id', PARAM_INT);
        $backurl = new moodle_url(
            '/local/coursetransfer/origin_restore_category.php',
            ['id' => $this->category->id, 'new' => 1, 'step' => 3, 'site' => $siteposition, 'restoreid' => $restoreid]
        );
        $tableurl = new moodle_url(
            '/local/coursetransfer/origin_restore_category.php',
            ['id' => $this->category->id]
        );
        $data = new stdClass();
        $data->button = false;
        $data->restoreid = $restoreid;
        $data->destinyid = $destinyid;
        $data->siteposition = $siteposition;
        $data->steps = [ ["current" => false, "num" => 1], ["current" => false, "num" => 2],
            ["current" => false, "num" => 3], ["current" => true, "num" => 4] ];
        $data->back_url = $backurl->out(false);
        $data->table_url = $tableurl->out(false);
        $site = coursetransfer::get_site_by_position($siteposition);
        $data->host = $site->host;
        if (coursetransfer::validate_origin_site($site->host)) {
            $data->haserrors = false;
            try {
                $request = new request($site);
                $res = $request->origin_get_category_detail($restoreid);
                if ($res->success) {
                    $data->category = $res->data;
                } else {
                    $data->errors = $res->errors;
                    $data->haserrors = true;
                }
            } catch (moodle_exception $e) {
                $data->errors = ['code' => '200121', 'msg' => $e->getMessage()];
                $data->haserrors = true;
            }
        } else {
            $data->haserrors = true;
            $errors[] = ['code' => '200122', 'msg' => get_string('error_validate_site', 'local_coursetransfer')];
            $data->errors = $errors;
        }
        $data->category->sessionStorage_id = "local_coursetransfer_".$this->category->id."_".$data->restoreid;
        return $data;
    }

}
