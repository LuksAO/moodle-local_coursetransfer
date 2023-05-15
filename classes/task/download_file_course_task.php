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
 * This file defines an adhoc task to create a backup of the curse.
 *
 * @package    mod_forum
 * @copyright  2023 3iPunt <https://www.tresipunt.com/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_coursetransfer\task;

use async_helper;
use file_exception;
use local_coursetransfer\api\request;
use local_coursetransfer\coursetransfer;
use moodle_exception;
use stdClass;
use stored_file_creation_exception;

/**
 * Create Backup Course Task
 */
class download_file_course_task extends \core\task\adhoc_task {

    // Use the logging trait to get some nice, juicy, logging.
    use \core\task\logging_trait;


    /**
     * Execute the task.
     *
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    public function execute() {
        global $DB;

        $this->log_start("Download File Course Starting...");
        // TODO. Call restore_course_task.
        $this->log_finish("Download File Course Finishing...");

        mtrace('Download completed');
    }

}