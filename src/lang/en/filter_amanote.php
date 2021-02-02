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
 * English strings for Amanote filter.
 *
 * @package     filter_amanote
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['filtername'] = 'Amanote';

// Settings.
$string['pluginadministration'] = 'Amanote module administration';

$string['autosaveperiod'] = 'Auto-save period';
$string['autosaveperiod_help'] = 'Configure the period of time in minutes between automatic saves (min.: 1, max.: 30). Setting the period to 0 means no auto-save.';

$string['saveinprivate'] = 'Save notes in private files';
$string['saveinprivate_help'] = 'Save the annotated file in the private files of the user. This will allow the user to get back its notes the next time he opens the annotatable file in Amanote.';

$string['key'] = 'Activation key';
$string['key_help'] = 'This key is required for advanced features such as Podcast Creator.';

$string['importantinformationheading'] = 'Important installation information';
$string['importantinformationdescription'] = 'In order for the module to work properly, please check that the following requirements are met on your Moodle site:

1. Amanote filter is enabled (Site administration > Plugins > Filters > Manage filters)

2. Web services are enabled (Site administration > Advanced feature)

3. *Moodle mobile web service* is enabled (Site administration > Plugins > Web services > External services)

4. REST protocol is activated (Site administration > Plugins > Web services > Manage protocols)

5. Capability *webservice/rest:use* is allowed for *authenticated users* (Site administration > Users > Permissions > Define Roles > Authenticated Users > Manage roles)';

// Core.
$string['openinamanote'] = 'Open in Amanote';
$string['downloadnotes'] = 'Download annotated file';
$string['openanalytics'] = 'Open Learning Analytics';
$string['openpodcast'] = 'Open Podcast Creator';
$string['teacher'] = 'Teacher';

// Privacy.
$string['privacy:metadata'] = 'In order to integrate with Amanote, some user data need to be sent to the Amanote client application (remote system).';
$string['privacy:metadata:userid'] = 'The userid is sent from Moodle to Amanote in order to speed up the authentication process.';
$string['privacy:metadata:fullname'] = 'The user\'s full name is sent to the remote system to allow a better user experience.';
$string['privacy:metadata:email'] = 'The user\'s email is sent to the remote system to allow a better user experience (note sharing, etc.).';
$string['privacy:metadata:access_token'] = 'The user\'s access token is required to save the notes in the Moodle\'s private files space.';
$string['privacy:metadata:access_token_expiration'] = 'The access token\'s expiration is sent to prevent the user to use the app with an expired token.';
$string['privacy:metadata:subsystem:corefiles'] = 'Files (PDF, AMA) are stored using Moodle\'s file system.';
