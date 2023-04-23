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
 * Initialise and highlight code snippets.
 *
 * @module    mod_snippet/hljs
 * @copyright 2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Import the highlight.js library.
import hljs from 'mod_snippet/highlight';

/**
 * Initialise and highlight.js and load the language syntax module.
 *
 * @param {array} languages The languages used in the snip or list of snips.
 */
export const init = (languages) => {
    for (const language of languages) {

        // Load snip's language syntax module.
        import(`mod_snippet/languages/${language}`).then((liblanguage) => {

            // Register the snip's language syntax module.
            hljs.registerLanguage(language, liblanguage);

            // Highlight all code blocks with the loaded language.
            document.querySelectorAll(`pre code.language-${language}`).forEach((el) => {
                hljs.highlightElement(el);
            });
        });
    }
};
