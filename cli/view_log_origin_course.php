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
 * CLI script
 *
 *
 * @package     local_coursetransfer
 * @copyright   2023 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_coursetransfer\coursetransfer;

define('CLI_SCRIPT', 1);

require(__DIR__.'/../../../config.php');
global $CFG;
require_once($CFG->libdir . '/clilib.php');

$usage = 'CLI para ver los logs de peticiones de restauraciones de un curso desde otro Moodle.

Usage:
    # php view_log_origin_course.php
        --courseid=<courseid>

    --courseid=<courseid>  Origin Course ID (int)

Options:
    -h --help                   Print this help.

Description.

Examples:

    # php local/coursetransfer/cli/view_log_origin_course.php --courseid=3
';

list($options, $unrecognised) = cli_get_params([
        'help' => false,
        'courseid' => null,
], [
        'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

$courseid = (int) $options['courseid'];

if ( $courseid === null ) {
    cli_writeln( get_string('courseid_require', 'local_coursetransfer') );
    exit(128);
} else if ( $courseid <= 0 ) {
    cli_writeln( get_string('courseid_integer', 'local_coursetransfer') );
    exit(128);
}

try {

    $mask = "| %12.15s |%-35.35s | %-14.14s | %-14.14s  | %-7.7s | %-50.50s  | %-8.8s  | %-7.7s  | %-15.15s  | %-15.15s |\n";
    printf($mask,
            'Request ID', 'Destiny Site', 'Dest Course', 'Orig Course',
            'Status', 'Error', 'Size', 'UserID', 'TimeModified', 'TimeCreated');

    foreach (\local_coursetransfer\coursetransfer_request::get_by_origin_course_id($courseid) as $item) {
        printf($mask,
                $item->id, $item->siteurl, $item->destiny_course_id, $item->origin_course_id,
                get_string('status_' . coursetransfer::STATUS[$item->status]['shortname'], 'local_coursetransfer'),
                $item->error_code . ': ' . $item->error_message,
                $item->origin_backup_size, $item->userid, $item->timemodified, $item->timecreated);
    }
    exit(0);

} catch (moodle_exception $e) {
    cli_writeln('200700: ' . $e->getMessage());
    exit(1);
}

