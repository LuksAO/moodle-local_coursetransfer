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
use local_coursetransfer\api\request;
use local_coursetransfer\coursetransfer;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/group/lib.php');

class restore_course_external extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function new_origin_restore_course_step1_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'siteurl' => new external_value(PARAM_RAW, 'Site Url')
            )
        );
    }

    /**
     * Check if user exists
     *
     * @param string $siteurl
     *
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function new_origin_restore_course_step1(string $siteurl): array {
        self::validate_parameters(
            self::new_origin_restore_course_step1_parameters(),
            [
                'siteurl' => $siteurl
            ]
        );

        $success = true;
        $errors = [];
        $data = new stdClass();

        try {
            $success = false;
            $request = new request($siteurl);
            $res = $request->origin_has_user();
        } catch (moodle_exception $e) {
            $success = false;
            $errors[] =
                [
                    'code' => '030340',
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
    public static function new_origin_restore_course_step1_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'errors' => new external_multiple_structure(new external_single_structure(
                    array(
                        'code' => new external_value(PARAM_TEXT, 'Code'),
                        'msg' => new external_value(PARAM_TEXT, 'Message')
                    ),
                    PARAM_TEXT,
                    'Errors'
                )),
                'data' => new external_single_structure(
                    array(
                        'userid' => new external_value(PARAM_INT, 'User ID', VALUE_OPTIONAL),
                        'username' => new external_value(PARAM_TEXT, 'Username', VALUE_OPTIONAL),
                        'firstname' => new external_value(PARAM_TEXT, 'Firstname', VALUE_OPTIONAL),
                        'lastname' => new external_value(PARAM_TEXT, 'Lastname', VALUE_OPTIONAL),
                        'email' => new external_value(PARAM_TEXT, 'Email', VALUE_OPTIONAL),
                        'nexturl' => new external_value(PARAM_RAW, 'Next URL', VALUE_OPTIONAL)
                    ),
                    PARAM_TEXT,
                    'Data'
                )
            )
        );
    }
};