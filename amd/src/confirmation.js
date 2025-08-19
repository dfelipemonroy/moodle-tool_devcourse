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
 * Confirms the deletion of an entry
 *
 * @module     tool_devcourse/confirmation
 * @copyright  2025 Diego Monroy <diego.monroy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as str from 'core/str';
import * as notification from 'core/notification';
import * as ajax from 'core/ajax';
import * as templates from 'core/templates';

/**
 * Displays a confirmation dialog to the user before deleting an entry.
 * If the user confirms, proceeds to delete the entry and update the entries list.
 *
 * @param {number|string} id - The identifier of the entry to be deleted.
 * @param {Array} entriesList - The list of entries to update after deletion.
 */
export const confirmDeletion = (id, entriesList) => {
    let pluginname = 'tool_devcourse';

    str.get_strings([
        {key: 'delete'},
        {key: 'confirmdeleteentry', component: pluginname},
        {key: 'yes'},
        {key: 'no'}
    ])
    .then(strings => {
        // Show confirmation dialog. If user confirms, redirect to the given URL.
        notification.confirm(strings[0], strings[1], strings[2], strings[3], () => {
            processDeleteEntry(id, entriesList);
        });
        return null;
    })
    // Handle any errors that occur during the string retrieval or confirmation dialog.
    .catch(notification.exception);
};

/**
 * Calls the services to delete the entry and retrieve the entries list
 *
 * @method processDeleteEntry
 * @param {Number} id
 * @param {Object} entriesList
 */
export const processDeleteEntry = (id, entriesList) => {
    const courseid = entriesList.dataset.courseid;
    // eslint-disable-next-line
    console.log(`Deleting entry ${id} from course ${courseid}`);
    ajax.call([
        {
            methodname: 'tool_devcourse_delete_entry',
            args: {id: id}
        }
    ])[0]
        .then(() => {
            // eslint-disable-next-line
            console.log(`Entry ${id} deleted successfully.`);
            return ajax.call([
                {
                    methodname: 'tool_devcourse_list_entries',
                    args: {courseid: courseid}
                }
            ])[0];
        })
        .then(data => {
            // eslint-disable-next-line
            console.log(`DATA: ${JSON.stringify(data)}`, entriesList);
            loadList(data, entriesList);
            return null;
        })
        .catch(notification.exception);
};

/**
 * Loads and renders the entries list
 *
 * @method loadList
 * @param {Object} data
 * @param {Object} entriesList
 */
export const loadList = (data, entriesList) => {
    templates.render('tool_devcourse/entries_list', data)
        .then(([html]) => {
            entriesList.replaceWith(html);
            return null;
        })
        .catch(notification.exception);
};

/**
 * Attaches a click handler to all elements matching the selector to trigger confirmation.
 * @param {string} selector - CSS selector for elements that trigger the confirmation dialog.
 */
export const clickHandler = (selector) => {
    const items = document.querySelectorAll(selector);

    items.forEach((item) => {
        item.addEventListener("click", (e) => {
            e.preventDefault();
            const id = item.dataset.entryid;
            const entriesList = item.closest('.entries_list');
            confirmDeletion(id, entriesList);
        });
    });
};

/**
 * Initializes the deletion confirmation functionality for selected elements.
 * @param {string} selector - CSS selector for elements that trigger the confirmation dialog.
 */
export const init = (selector) => {
    clickHandler(selector);
};
