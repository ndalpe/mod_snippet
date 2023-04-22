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
 * Snip management class.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_snippet\local;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * Class to manage snips.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snips {

    /**
     * Create a new snip.
     *
     * @param stdClass $data The data to create the snip.
     *
     * @return stdClass The new snip id.
     */
    public static function create($snipformdata):int {
        global $DB, $USER;

        $time = time();

        // Create a new category if firstcategory hidden field is set to 'yes'.
        if ($snipformdata->firstcategory == 'yes') {
            $category = new stdClass();
            $category->userid = $USER->id;
            $category->name = $snipformdata->categoryname;
            $category->timecreated = $time;
            $category->timemodified = $time;

            $snipformdata->categoryid = $DB->insert_record('snippet_categories', $category);
        } else {
            $snipformdata->categoryid = (int) $snipformdata->categoryid;
        }

        // Clean up.
        unset($snipformdata->firstcategory);

        // Create a new snip object.
        $snipformdata->snippetid = $snipformdata->id;
        $snipformdata->userid = $USER->id;
        $snipformdata->timecreated = $time;
        $snipformdata->timemodified = $time;

        // Insert the new snip into the database.
        $snipformdata->id = $DB->insert_record(
            'snippet_snips',
            $snipformdata
        );

        // Return the new snip.
        return $snipformdata->id;
    }

    /**
     * Create a new snip.
     *
     * @param stdClass $data The data to create the snip.
     *
     * @return stdClass The new snip id.
     */
    public static function update($data): int {
        global $DB, $USER;

        // Create a new snip object.
        $snip = new \stdClass();
        $snip->name = $data->name;
        $snip->description = $data->description;
        $snip->categoryid = $data->categoryid;
        $snip->userid = $USER->id;
        $snip->timecreated = time();
        $snip->timemodified = time();

        // Insert the new snip into the database.
        $snip->id = $DB->insert_record('snippet_snips', $snip);

        // Return the new snip.
        return $snip->id;
    }

    /**
     * Get the snip count for a given category.
     *
     * @param int $categoryid The category id.
     *
     * @return int The snippet count.
     */
    public static function get_snip_count($categoryid): int {
        global $DB;

        return $DB->count_records('snippet_snips', ['categoryid' => $categoryid]);
    }
}
