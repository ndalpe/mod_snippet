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
 * Categories management class.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_snippet\local;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_snippet course module viewed event class.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categories {

    /**
     * Get the user categories.
     *
     * @param int $userid The user id.
     *
     * @return array The list of categories.
     */
    public static function get_category_list($userid):array {
        global $DB;

        $categories = array();

        $records = $DB->get_records('snippet_categories', ['userid' => $userid], 'name ASC');

        foreach ($records as $record) {
            $categories[$record->id] = $record->name;
        }

        return $categories;
    }

    /**
     * Check if the user has at least one category.
     *
     * @param int $userid The user id.
     *
     * @return bool True if the user has at least one category.
     */
    public static function has_category($userid):bool {
        global $DB;

        $record = $DB->get_record('snippet_categories', ['userid' => $userid]);

        return $record !== false;
    }
}
