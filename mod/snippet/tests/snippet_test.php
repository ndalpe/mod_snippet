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
 * Unit tests for mod_snippet lib
 *
 * @package mod_page
 * @category external
 * @copyright 2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.1
 */

namespace mod_snippet;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for mod_snippet.
 *
 * @package mod_snippet
 * @category external
 * @copyright 2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.1
 * @group mod_snippet
 */
class mod_snippet_test extends \advanced_testcase {

    /**
     * Test the snippet instance creation.
     */
    public function test_create_snippet() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $snippet = $this->getDataGenerator()->create_module(
            'snippet',
            array('course' => $course->id)
        );

        $this->assertEquals(1, $DB->count_records('snippet'));

        $cm = get_coursemodule_from_instance('snippet', $snippet->id);
        $this->assertEquals($snippet->id, $cm->instance);
        $this->assertEquals('snippet', $cm->modname);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($snippet->cmid, $context->instanceid);
    }

    /**
     * Test the snippet instance update.
     */
    public function test_update_snippet() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $snippet = $this->getDataGenerator()->create_module(
            'snippet',
            array('course' => $course->id)
        );

        $this->assertEquals(1, $DB->count_records('snippet'));

        // Create new snippet data.
        $snippet->instance = $snippet->id;
        $snippet->name = 'New name';
        $snippet->intro = 'New intro';
        $snippet->introformat = FORMAT_PLAIN;
        $snippet->timecreated = 1000;

        // Update the snippet instance.
        $this->assertTrue(snippet_update_instance($snippet));

        // Get the updated snippet data.
        $newsnippet = $DB->get_record('snippet', array('id' => $snippet->id));

        // Make sure the snippet data was updated.
        $this->assertEquals('New name', $newsnippet->name);
        $this->assertEquals('New intro', $newsnippet->intro);
        $this->assertEquals(FORMAT_PLAIN, $newsnippet->introformat);
        $this->assertEquals(1000, $newsnippet->timecreated);
    }

    /**
     * Test the snippet instance deletion.
     */
    public function test_delete_snippet() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $snippet = $this->getDataGenerator()->create_module(
            'snippet',
            array('course' => $course->id)
        );

        $this->assertEquals(1, $DB->count_records('snippet'));

        // Create new snippet data.
        $snippet->instance = $snippet->id;
        $snippet->name = 'New name';
        $snippet->intro = 'New intro';
        $snippet->introformat = FORMAT_PLAIN;
        $snippet->timecreated = 1000;

        // Update the snippet instance.
        $this->assertTrue(snippet_update_instance($snippet));

        // Get the updated snippet data.
        $newsnippet = $DB->get_record('snippet', array('id' => $snippet->id));

        // Make sure the snippet data was updated.
        $this->assertEquals('New name', $newsnippet->name);
        $this->assertEquals('New intro', $newsnippet->intro);
        $this->assertEquals(FORMAT_PLAIN, $newsnippet->introformat);
        $this->assertEquals(1000, $newsnippet->timecreated);
    }
}
