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
 * Snipet category manager.
 *
 * @module     mod_snippet/category
 * @class      Category
 * @copyright 2023 Nicolas Dalpe <ndalpe@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import { get_string as getString } from 'core/str';

const selectors = {
    showNewCategoryButton: '[data-action="createcategorymodal"]',
};

/**
 * Initialise and highlight.js and load the language syntax module.
 */
export const init = () => {

    const saveAsPresetButton = document.querySelector(selectors.showNewCategoryButton);
    const modalForm = new ModalForm({
        modalConfig: {
            title: getString('newcategory_modal_title', 'mod_snippet'),
            large: true,
        },
        args: { name: saveAsPresetButton.dataset.dataid },
        saveButtonText: getString('ok', 'core_moodle'),
        formClass: 'mod_snippet\\form\\category_add',
    });

    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
        if (event.detail.result) {
            console.log('form sent');
        } else {
            Notification.addNotification({
                type: 'error',
                message: event.detail.errors.join('<br>')
            });
        }
    });

    modalForm.show();

};