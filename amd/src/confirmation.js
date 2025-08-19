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

/**
 * Shows a confirmation dialog for deletion and redirects if confirmed.
 * @param {string} url - The URL to redirect to if deletion is confirmed.
 */
export const confirmDeletion = (url) => {
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
            window.location.href = url;
        });
        return null;
    })
    // Handle any errors that occur during the string retrieval or confirmation dialog.
    .catch(notification.exception);
};

/**
 * Attaches a click handler to all elements matching the selector to trigger confirmation.
 * @param {string} selector - CSS selector for elements that trigger the confirmation dialog.
 */
export const onClickHandler = (selector) => {
    document.querySelectorAll(selector).forEach(item => {
        item.addEventListener('click', event => {
            // Prevent the default link behavior.
            event.preventDefault();
            const href = item.getAttribute('href');
            if (href) {
                confirmDeletion(href);
            }
        });
    });
};

/**
 * Initializes the deletion confirmation functionality for selected elements.
 * @param {string} selector - CSS selector for elements that trigger the confirmation dialog.
 */
export const init = (selector) => {
    onClickHandler(selector);
};
