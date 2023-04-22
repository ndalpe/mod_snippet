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

use mod_snippet\local\snips;

/**
 * The mod_snippet course module viewed event class.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categories {

    /**
     * Get the user categories for a given user.
     *
     * @param int $userid The user id.
     *
     * @return array The list of categories.
     */
    public static function get_category_list_for_user(int $userid):array {
        global $DB;

        $records = $DB->get_records('snippet_categories', ['userid' => $userid], 'name ASC');

        return $records;
    }

    /**
     * Get a list of categories, formated for <select> menu, for a given user.
     *
     * @param int $userid The user id.
     *
     * @return array The list of categories.
     */
    public static function get_category_list_for_input(int $userid):array {
        global $DB;

        $categories = $DB->get_records_menu(
            'snippet_categories', ['userid' => $userid], 'name ASC', 'id, name'
        );

        return $categories;
    }

    /**
     * Get a list of categories, formated for nav menu, for a given user.
     *
     * @param int $userid The user id.
     *
     * @return array The list of categories.
     */
    public static function get_category_list_for_nav(int $userid):array {

        $categories = self::get_category_list_for_user($userid);

        foreach ($categories as $key => $category) {
            // Get the snippet count for each category.
            $categories[$key]->count = snips::get_snip_count($category->id);

            // If the category contains no snippet, set hasnosnippet to true.
            $categories[$key]->hasnosnippet = ($categories[$key]->count == 0);
        }

        return array_values($categories);
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

        $record = $DB->record_exists('snippet_categories', ['userid' => $userid]);

        return $record;
    }
}
