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
 * Confirms the deletion of an entry.
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
    str.get_strings([
            {key: 'delete'},
            {key: 'confirmdeleteentry', component: 'tool_devcourse'},
            {key: 'yes'},
            {key: 'no'}
        ])
        .then(function(strings) {
            return notification.confirm(strings[0], strings[1], strings[2], strings[3], function() {
                processDeleteEntry(id, entriesList);
            });
        })
        .catch(notification.exception);
};

/**
 * Deletes an entry by its ID and reloads the list of entries for a given course.
 *
 * @param {number|string} id - The ID of the entry to delete.
 * @param {HTMLElement} entriesList - The DOM element representing the list, which must have a 'data-courseid' attribute.
 */
export const processDeleteEntry = (id, entriesList) => {
    let courseid = entriesList.dataset.courseid;
    let requests = ajax.call([
        {
            methodname: 'tool_devcourse_delete_entry',
            args: {id: id}
        },
        {
            methodname: 'tool_devcourse_list_entries',
            args: {courseid: courseid}
        }
    ]);
    requests[1].then(function(data) {
        // We reload DOM.
        return reloadList(data, entriesList);
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
export const reloadList = (data, entriesList) => {
    templates.render('tool_devcourse/entries_list', data)
    .then(function(html, js) {
        return templates.replaceNodeContents(entriesList, html, js);
    })
    .catch(notification.exception);
};

/**
 * Attaches a click handler to all elements matching the selector to trigger confirmation.
 * @param {string} selector - CSS selector for elements that trigger the confirmation dialog.
 */
export const clickHandler = (selector) => {
    let items = document.querySelectorAll(selector);

    items.forEach((item) => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            let id = item.dataset.entryid;
            let entriesList = item.closest('.entries_list');
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
