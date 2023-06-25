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

        $snipdata = new \stdClass();
        $snipdata->id = $cm->id;
        $snipdata->categoryid = $categoryid;
        $snipdata->userid = $user->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'This is the name';
        $snipdata->intro = ['text' => 'This is the intro', 'format' => FORMAT_PLAIN];
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
        $snipdata->intro = ['text' => 'This is the intro', 'format' => FORMAT_PLAIN];
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

        $snipdata = new \stdClass();
        $snipdata->id = $cm->id;
        $snipdata->userid = $user->id;
        $snipdata->snipid = 0;
        $snipdata->name = 'This is the name';
        $snipdata->intro = ['text' => 'This is the intro', 'format' => FORMAT_PLAIN];
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
        $snipdata->name = 'Initial name';
        $snipdata->intro = ['text' => 'Initial description', 'format' => FORMAT_PLAIN];
        $snipdata->firstcategory = 'no';
        $snipdata->private = '1';
        $snipdata->language = 'PHP';
        $snipdata->snippet = '<?php php_info(); ?>';
        $snipdata->timecreated = $time;
        $snipdata->timemodified = $time;

        $snipid = snips::create($snipdata);

        // Make sure the snip was created.
        $this->assertEquals(1, $DB->count_records('snippet_snips', ['id' => $snipid]));

        // Compare the data from $snipdata with the data from the database.
        $createdsnip = $DB->get_record('snippet_snips', array('id' => $snipid));
        foreach ($snipdata as $key => $value) {
            $this->assertEquals($value, $createdsnip->{$key});
        }

        // Update the snip data.
        $snipdata->snipid = $snipid;
        $snipdata->categoryid = $category2id;
        $snipdata->name = 'Updated name';
        $snipdata->intro = ['text' => 'Updated description', 'format' => FORMAT_PLAIN];
        $snipdata->private = '0';
        $snipdata->language = 'javascript';
        $snipdata->snippet = 'export default SELECTORS;';
        $snipupdate = snips::update($snipdata);
        $this->assertTrue(boolval($snipupdate));

        // Get the updated snip data.
        $updatedsnip = $DB->get_record('snippet_snips', array('id' => $snipid));

        // Make sure the snippet data was updated.
        $this->assertEquals($category2id, $updatedsnip->categoryid);
        $this->assertEquals($snipdata->name, $updatedsnip->name);
        $this->assertEquals($snipdata->intro['text'], $updatedsnip->intro);
        $this->assertEquals($snipdata->private, $updatedsnip->private);
        $this->assertEquals($snipdata->language, $updatedsnip->language);
        $this->assertEquals($snipdata->snippet, $updatedsnip->snippet);
    }

    public function test_get_snip_count_for_category() {
        global $DB;
        $this->resetAfterTest(true);

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
        $user = $generator->create_user();

        // Create a categories.
        $categoryid = $snippetgenerator->create_category(
            ['snippetid' => $snippet->id, 'userid' => $user->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet_categories', ['id' => $categoryid]));

        $snipparam = ['categoryid' => $categoryid, 'userid' => $user->id, 'snippetid' => $snippet->id];
        $snip1 = $snippetgenerator->create_snip($snipparam);
        $snip2 = $snippetgenerator->create_snip($snipparam);
        $snip3 = $snippetgenerator->create_snip($snipparam);

        $numsnip = snips::get_snip_count_for_category($user->id, $categoryid);
        $this->assertIsInt($numsnip);
        $this->assertEquals(3, $numsnip);
    }

    public function test_get_latest_snips() {
        global $DB;
        $this->resetAfterTest(true);

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
        $user = $generator->create_user();

        // Create a categories.
        $categoryid = $snippetgenerator->create_category(
            ['snippetid' => $snippet->id, 'userid' => $user->id]
        );
        $this->assertEquals(1, $DB->count_records('snippet_categories', ['id' => $categoryid]));

        // Create 15 snips.
        $snipparam = ['categoryid' => $categoryid, 'userid' => $user->id, 'snippetid' => $snippet->id];
        for ($i = 0; $i < 15; $i++) {
            $snippetgenerator->create_snip($snipparam);
        }

        $latestsnip = snips::get_latest_snips($user->id, 5);
        $this->assertEquals(5, count($latestsnip));

        $latestsnip = snips::get_latest_snips($user->id);
        $this->assertEquals(10, count($latestsnip));

        $this->setUser($user);
        $latestsnip = snips::get_latest_snips();
        $this->assertEquals(10, count($latestsnip));
    }
}
