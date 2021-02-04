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
 * Amanote filter admin settings and defaults.
 *
 * @package     filter_amanote
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // General settings.
    $settings->add(new admin_setting_configtext('filter_amanote/autosaveperiod',
        get_string('autosaveperiod', 'filter_amanote'),
        get_string('autosaveperiod_help', 'filter_amanote'), 5));

    $settings->add(new admin_setting_configcheckbox('filter_amanote/saveinprivate',
        get_string('saveinprivate', 'filter_amanote'),
        get_string('saveinprivate_help', 'filter_amanote'), 1));

    $settings->add(new admin_setting_configtext('filter_amanote/key',
        get_string('key', 'filter_amanote'),
        get_string('key_help', 'filter_amanote'), ''));

    // Important information.
    $settings->add(new admin_setting_heading('amanoteimportantinformation',
        get_string('importantinformationheading', 'filter_amanote'),
        get_string('importantinformationdescription', 'filter_amanote')));
}
