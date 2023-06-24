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
 * Unit tests for mod_snippet.
 *
 * @package mod_snippet
 * @copyright 2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.1
 * @group mod_snippet
 */

namespace mod_snippet;

defined('MOODLE_INTERNAL') || die();

use mod_snippet\local\snips;

/**
 * Unit tests for mod_snippet.
 *
 * @package mod_snippet
 * @copyright 2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.1
 * @group mod_snippet
 */
class snips_test extends \advanced_testcase {

    /**
     * Test the snip instance creation.
     */
    public function test_create_snip() {
        global $DB;
        $this->resetAfterTest(true);

        $time = time();

        $generator = $this->getDataGenerator();
        $snippetgenerator = $generator->get_plugin_generator('mod_snippet');

        // Create a course and a snippet.
        $course = $generator->create_course();
        $snippet = $generator->create_module(
            'snippet', ['course' => $course->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet'));

        $cm = get_coursemodule_from_instance('snippet', $snippet->id);
        $this->assertEquals($snippet->id, $cm->instance);
        $this->assertEquals('snippet', $cm->modname);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($snippet->cmid, $context->instanceid);

        // Create a user.
        $user = $generator->create_user();

        // Create a category.
        $categoryid = $snippetgenerator->create_category(
            ['snippetid' => $snippet->id, 'userid' => $user->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet_categories', ['id' => $categoryid]));

        $description = [
            'text' => 'This is the description',
            'format' => FORMAT_PLAIN
        ];

        $snipdata = new \stdClass();
        $snipdata->id = $cm->id;
        $snipdata->categoryid = $categoryid;
        $snipdata->userid = $user->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'This is the name';
        $snipdata->description = $description;
        $snipdata->firstcategory = 'no';
        $snipdata->timecreated = $time;
        $snipdata->timemodified = $time;

        $snip1id = snips::create($snipdata);

        $this->assertEquals(1, $DB->count_records('snippet_snips', ['id' => $snip1id]));

        $snipdata = new \stdClass();
        $snipdata->id = $cm->id;
        $snipdata->categoryid = $categoryid;
        $snipdata->userid = $user->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'This is the name';
        $snipdata->description = $description;
        $snipdata->firstcategory = 'no';
        $snipdata->timecreated = $time;
        $snipdata->timemodified = $time;

        $snip2id = snips::create($snipdata);
        $this->assertEquals(1, $DB->count_records('snippet_snips', ['id' => $snip2id]));

        $this->assertEquals(2, $DB->count_records('snippet_snips'));
    }

    /**
     * Test the snip instance creation.
     */
    public function test_create_snip_with_first_category() {
        global $DB;
        $this->resetAfterTest(true);

        $time = time();

        $generator = $this->getDataGenerator();

        // Create a course and a snippet.
        $course = $generator->create_course();
        $snippet = $generator->create_module(
            'snippet',
            ['course' => $course->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet'));

        $cm = get_coursemodule_from_instance('snippet', $snippet->id);
        $this->assertEquals($snippet->id, $cm->instance);
        $this->assertEquals('snippet', $cm->modname);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($snippet->cmid, $context->instanceid);

        // Create a user.
        $user = $generator->create_user();

        $this->assertEquals(0, $DB->count_records('snippet_categories'));

        $description = [
            'text' => 'This is the description',
            'format' => FORMAT_PLAIN
        ];

        $snipdata = new \stdClass();
        $snipdata->id = $cm->id;
        $snipdata->userid = $user->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'This is the name';
        $snipdata->description = $description;
        $snipdata->firstcategory = 'yes';
        $snipdata->categoryname = 'This is the category';
        $snipdata->timecreated = $time;
        $snipdata->timemodified = $time;

        $snip1id = snips::create($snipdata);
        $this->assertEquals(1, $DB->count_records('snippet_snips', ['id' => $snip1id]));

        // Make sure the category was created.
        $this->assertEquals(1, $DB->count_records('snippet_categories'));
    }

    /**
     * Test the snip instance creation.
     */
    public function test_update_snip() {
        global $DB;
        $this->resetAfterTest(true);

        $time = time();

        $generator = $this->getDataGenerator();
        $snippetgenerator = $this->getDataGenerator()->get_plugin_generator('mod_snippet');

        // Create a course and a snippet.
        $course = $generator->create_course();
        $snippet = $generator->create_module(
            'snippet',
            ['course' => $course->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet'));

        $cm = get_coursemodule_from_instance('snippet', $snippet->id);
        $this->assertEquals($snippet->id, $cm->instance);
        $this->assertEquals('snippet', $cm->modname);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($snippet->cmid, $context->instanceid);

        // Create a user.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();

        // Create 2 categories.
        $category1id = $snippetgenerator->create_category(
            ['snippetid' => $snippet->id, 'userid' => $user1->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet_categories', ['id' => $category1id]));

        $category2id = $snippetgenerator->create_category(
            ['snippetid' => $snippet->id, 'userid' => $user1->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet_categories', ['id' => $category2id]));

        // Create the first snip.
        $snipdata = new \stdClass();
        $snipdata->id = $cm->id;
        $snipdata->categoryid = $category1id;
        $snipdata->userid = $user1->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'First name';
        $snipdata->description = ['text' => 'First description', 'format' => FORMAT_PLAIN];
        $snipdata->firstcategory = 'no';
        $snipdata->timecreated = $time;
        $snipdata->timemodified = $time;

        $snip1id = snips::create($snipdata);
        $this->assertEquals(1, $DB->count_records('snippet_snips', ['id' => $snip1id]));

        // Update the snip.
        $snipdata = new \stdClass();
        $snipdata->snippetid = $snippet->id;
        $snipdata->categoryid = $category2id;
        $snipdata->userid = $user2->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'Updated name';
        $snipdata->description = ['text' => 'Updated description', 'format' => FORMAT_PLAIN];

        $snip1id = snips::update($snipdata);
        $this->assertEquals(1, $DB->count_records('snippet_snips', ['id' => $snip1id]));
        $this->assertIsInt($snip1id);

        // Get the updated snippet data.
        $updatedsnip = $DB->get_record('snippet', array('id' => $snip1id));

        // Make sure the snippet data was updated.
        $this->assertEquals($category2id, $updatedsnip->categoryid);
        $this->assertEquals($user2->id, $updatedsnip->userid);
        $this->assertEquals('Updated name', $updatedsnip->name);
        $this->assertEquals('Updated description', $updatedsnip->intro);
    }
}