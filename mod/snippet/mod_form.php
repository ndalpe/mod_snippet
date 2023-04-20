<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_snippet configuration form.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

use mod_snippet\local\languages;
use mod_snippet\local\categories;
use mod_snippet\local\manager;

/**
 * Module instance settings form.
 *
 * @package     mod_snippet
 * @copyright   2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_snippet_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $USER;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('snippetname', manager::PLUGIN_NAME), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'snippetname', manager::PLUGIN_NAME);

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_snippet settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('header', 'snippetfieldset', get_string('snippetsettings', manager::PLUGIN_NAME));

        // Wheter the snippet is private of not.
        $mform->addElement('selectyesno', 'private', get_string('private', manager::PLUGIN_NAME));
        $mform->addHelpButton('private', 'private', 'snippet');
        $mform->setType('private', PARAM_BOOL);
        $mform->setDefault('private', 1);

        // Category of the snippet. Add a text field if the user has no category so he can create one.
        // Otherwise, add a select field with the user's categories.
        if (!categories::has_category($USER->id)) {
            $label = new lang_string('add_first_category', manager::PLUGIN_NAME);
            $mform->addElement('text', 'categoryid', $label);
            $mform->setType('categoryid', PARAM_TEXT);
        } else {
            $label = new lang_string('select_category', manager::PLUGIN_NAME);
            $categories = categories::get_category_list($USER->id);
            $mform->addElement( 'select', 'categoryid', $label, $categories);
            $mform->setType('categoryid', PARAM_INT);
        }

        // Programming language used in the snippet.
        $label = get_string('language', manager::PLUGIN_NAME);
        $languages = languages::get_languages_from_files();
        $options = [
            'multiple' => false,
            'noselectionstring' => get_string('select_language', manager::PLUGIN_NAME)
        ];
        $mform->addElement( 'autocomplete', 'language', $label, $languages, $options);
        $mform->setType('language', PARAM_TEXT);

        // The snippet content.
        $mform->addElement('textarea', 'snippet', get_string('snippet', manager::PLUGIN_NAME), array('row' => '10'));
        $mform->setType('language', PARAM_TEXT);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
