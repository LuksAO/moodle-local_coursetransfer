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
 * @package     local_coursetransfer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

namespace local_coursetransfer\external;

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use local_coursetransfer\coursetransfer;
use local_coursetransfer\coursetransfer_request;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/group/lib.php');

class origin_category_course_backup_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function origin_backup_category_course_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'field' => new external_value(PARAM_TEXT, 'Field'),
                'value' => new external_value(PARAM_TEXT, 'Value'),
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'destinycategoryid' => new external_value(PARAM_INT, 'Destiny Category ID'),
                'requestid' => new external_value(PARAM_INT, 'Request ID'),
                'destinysite' => new external_value(PARAM_TEXT, 'Destiny Site'),
            )
        );
    }

    /**
     * Backup of the course in origin
     *
     * @param string $field
     * @param string $value
     * @param int $courseid
     * @param int $destinycategoryid
     * @param int $requestid
     * @param string $destinysite
     * @param array $configuration
     * @param array $sections
     *
     *
     * @return array
     * @throws invalid_parameter_exception
     */
    public static function origin_backup_category_course(string $field, string $value, int $courseid, int $destinycategoryid,
            int $requestid, string $destinysite, array $configuration, array $sections = []): array {

        global $CFG;

        self::validate_parameters(
            self::origin_backup_category_course_parameters(), [
                'field' => $field,
                'value' => $value,
                'courseid' => $courseid,
                'destinycategoryid' => $destinycategoryid,
                'requestid' => $requestid,
                'destinysite' => $destinysite,
                'configuration' => $configuration,
                'sections' => $sections,
            ]
        );

        $errors = [];
        $data = new stdClass();
        $data->requestid = $requestid;
        $data->request_origin_id = null;
        $data->origin_backup_size_estimated = null;

        try {
            $authres = coursetransfer::auth_user($field, $value);
            if ($authres['success']) {
                $verifydestiny = coursetransfer::verify_destiny_site($destinysite);
                if ($verifydestiny['success']) {
                    $res = $authres['data'];
                    $object = new stdClass();
                    $object->type = 0;
                    $object->siteurl = $CFG->wwwroot;
                    $object->direction = 1;
                    $object->destiny_request_id = $requestid;
                    $object->request_category_id = null;
                    $object->origin_course_id = $courseid;
                    $object->origin_category_id = null;
                    // TODO. Revisar pliego.
                    $object->origin_enrolusers = 0;
                    $object->origin_remove_course = 0;
                    $object->origin_schedule_datetime = null;
                    $object->origin_remove_activities = 0;
                    // TODO.
                    $object->origin_activities = json_encode($sections);
                    $object->origin_backup_size = null;
                    $object->origin_backup_size_estimated = coursetransfer::get_backup_size_estimated($courseid);
                    $object->origin_backup_url = null;
                    $object->destiny_course_id = $destinycourseid;
                    $object->destiny_category_id = null;
                    $object->destiny_remove_activities = $configuration['destiny_remove_activities'];
                    $object->destiny_merge_activities = $configuration['destiny_merge_activities'];
                    $object->destiny_remove_enrols = $configuration['destiny_remove_enrols'];
                    $object->destiny_remove_groups = $configuration['destiny_remove_groups'];

                    $object->error_code = null;
                    $object->error_message = null;

                    $object->userid = $res->id;
                    $object->status = 10;

                    $requestoriginid = coursetransfer_request::insert_or_update($object);
                    coursetransfer::create_task_backup_course(
                            $courseid, $res->id, $verifydestiny['data'], $requestid, $requestoriginid);
                    $data->origin_backup_size_estimated = $object->origin_backup_size_estimated;
                    $data->request_origin_id = $requestoriginid;
                    $success = true;
                } else {
                    $success = false;
                    $errors[] = $verifydestiny['error'];
                }
            } else {
                $success = false;
                $errors[] = $authres['error'];
            }
        } catch (moodle_exception $e) {
            $success = false;
            $errors[] =
                [
                    'code' => '200031',
                    'msg' => $e->getMessage()
                ];
        }

        return [
            'success' => $success,
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function origin_backup_category_course_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'errors' => new external_multiple_structure(new external_single_structure(
                    array(
                        'code' => new external_value(PARAM_TEXT, 'Code'),
                        'msg' => new external_value(PARAM_TEXT, 'Message')
                    ), PARAM_TEXT, 'Errors'
                )),
                'data' => new external_single_structure(
                    array(
                        'requestid' => new external_value(PARAM_INT, 'Request ID', VALUE_OPTIONAL),
                        'request_origin_id' => new external_value(PARAM_INT, 'Request ID', VALUE_OPTIONAL),
                        'origin_backup_size_estimated' => new external_value(PARAM_INT,
                                'Backup Size Estimated (MB)', VALUE_OPTIONAL ),
                    ), PARAM_TEXT, 'Data'
                )
            )
        );
    }

};