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
 * The mod_snippet renderer.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_snippet\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class view_page implements renderable, templatable {

    /** @var stdClass $cm The course module object. */
    private $cm = null;

    public function __construct($cm) {
        $this->cm = $cm;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass $data The data to be used in the template.
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $DB, $USER;

        // Data for the template.
        $data = new stdClass();

        // The snippet course module id.
        $data->cmid = $this->cm->id;

        // Get the snippet content.
        $snippet = $DB->get_record('snippet', ['id' => $this->cm->instance]);
        if ($snippet !== false) {
            $data->language = $snippet->language;
            $data->snippet = $snippet->snippet;
        }

        // Get all the snippet categories for the given user.
        $data->categories = array_values(
            $DB->get_records('snippet_categories', ['userid' => $USER->id])
        );

        return $data;
    }
}