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
 * Implementation of the Amanote filter plugin.
 *
 * @package     filter_amanote
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');

/**
 * Filter for processing file links for Amanote enhancements.
 *
 * @copyright  2020 Amaplex Software
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_amanote extends moodle_text_filter
{
    /**
     * Set up the filter and insert JS scripts to page using amd.
     *
     * @param moodle_page $page
     * @param context $context
     */
    public function setup($page, $context) {
        try {
            global $PAGE, $COURSE;

            // Check if the filter is enabled for the current course.
            $enabledfilters = filter_get_active_in_context(context_course::instance($COURSE->id));
            if (!isset($enabledfilters['amanote'])) {
                return;
            }

            static $scriptinserted = false;

            // Insert script if needed.
            if ($scriptinserted ||
                (stripos($PAGE->pagetype, 'course-view') !== 0 && $PAGE->pagetype !== 'mod-folder-view') ||
                $PAGE->user_is_editing()) {
                return;
            }

            $this->insert_script($page);
            $scriptinserted = true;
        } catch (Exception $e) {
            debugging('An error occurred: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Insert the js script.
     *
     * @param moodle_page $page
     */
    private function insert_script($page) {
        global $CFG;

        $params = $this->generate_json_params();
        $userparams = $this->generate_json_user_params();

        // Insert user params using AMD.
        $page->requires->js_call_amd('filter_amanote/main', 'init', array($userparams));

        // Insert params in footer (AMD can't handle long params).
        $script = <<<EOF
            <script>
                var amanote_params = $params;
            </script>
EOF;

        if (!isset($CFG->additionalhtmlfooter)) {
            $CFG->additionalhtmlfooter = '';
        }

        $CFG->additionalhtmlfooter .= $script;
    }

    /**
     * Return the Moodle params needed by the js script in JSON format.
     *
     * @return string The params in JSON string format.
     */
    private function generate_json_params() {
        global $USER, $COURSE, $CFG, $OUTPUT;

        $config = get_config('filter_amanote');

        $siteurl         = $this->get_site_url();
        $language        = substr($USER->lang, 0, 2);
        $usercontext     = context_user::instance($USER->id);
        $privatefilepath = '/' . $usercontext->id . '/user/private/Amanote/';
        $files           = $this->get_course_files($COURSE);
        $moodleversion   = preg_replace('/(\d+\.\d+(\.\d+)?) .*$/', '$1', $CFG->release);

        return json_encode([
            'siteURL' => $siteurl,
            'privateFilePath' => $privatefilepath,
            'files' => $files,
            'moodle' => [
                'version' => $moodleversion,
            ],
            'plugin' => [
                'version' => $config->version,
                'autosavePeriod' => $config->autosaveperiod,
                'saveInProvider' => $config->saveinprivate,
                'key' => $config->key,
                'logo' => $OUTPUT->image_url('amanote-logo', 'filter_amanote')->__toString(),
            ],
            'language' => $language,
            'strings' => [
                'openInAmanote' => get_string('openinamanote', 'filter_amanote'),
                'downloadNotes' => get_string('downloadnotes', 'filter_amanote'),
                'openAnalytics' => get_string('openanalytics', 'filter_amanote'),
                'openPodcastCreator' => get_string('openpodcast', 'filter_amanote'),
                'teacher' => get_string('teacher', 'filter_amanote'),
            ]
        ]);
    }

    /**
     * Return the user params needed by the js script in JSON format.
     *
     * @return string The user params in JSON string format.
     */
    private function generate_json_user_params() {
        global $USER, $COURSE;

        $coursecontext   = context_course::instance($COURSE->id, MUST_EXIST);
        $isteacher       = has_capability('moodle/course:update', $coursecontext, $USER->id, false);
        $token           = $this->get_current_user_token();

        return json_encode([
            'id' => $USER->id,
            'token' => [
                'value' => $token,
            ],
            'isTeacher' => $isteacher,
        ]);
    }

    /**
     * Return the token of the current user.
     *
     * @return string The user token.
     */
    private function get_current_user_token() {
        global $USER, $DB;

        $service = $DB->get_record('external_services', array('name'=> 'amanote'), '*', MUST_EXIST);
        $context = context_system::instance();

        // Get existing user's tokens.
        $params = array(
            'userid' => $USER->id,
            'externalserviceid' => $service->id,
            'contextid' => $context->id
        );

        $usertokens = $DB->get_records('external_tokens', $params, 'validuntil ASC');

        if ($usertokens && count($usertokens) >= 1) {
            $usertoken = array_pop($usertokens);
            $minvaliduntil = time() + 60 * 30;

            if ($usertoken->validuntil == 0 || $usertoken->validuntil >= $minvaliduntil) {
                return $usertoken->token;
            }
        }

        // Create a new token if there isn't any valid token.
        return external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service, $USER->id, $context);
    }

    /**
     * Return the site url.
     *
     * @return string The site url (example: https://demo.moodle.com).
     */
    private function get_site_url() {
        global $CFG;

        // Check if protocol is HTTP.
        if (preg_match('/^http:\/\//', $CFG->wwwroot)) {

            $securewwwroot = preg_replace('/^http:\/\//', 'https://', $CFG->wwwroot, 1);

            // Check if Moodle site exists in HTTPS.
            $curl = curl_init($securewwwroot);
            if (curl_exec($curl)) {
                return $securewwwroot;
            }
            curl_close($curl);
        }

        return $CFG->wwwroot;
    }

    /**
     * Return all the files for a given course and component.
     *
     * @param course $course
     * @param string $component
     *
     * @return array List of files for the given course.
     */
    private function get_files_by_module($course, $component = 'mod_resource') {
        // Get the modules of the given kind for the given course.
        $modinfo = get_fast_modinfo($course);
        $modules = $modinfo->get_instances_of(str_replace('mod_', '', $component));

        $files = [];

        if (!empty($modules)) {

            $fs = get_file_storage();

            // Get the files of each module.
            foreach ($modules as $modid => $module) {
                $modulefiles = external_util::get_area_files($module->context->id, $component, 'content');

                foreach ($modulefiles as $modulefile) {
                    // Add only PDF files.
                    if ($modulefile['mimetype'] !== 'application/pdf') {
                        continue;
                    }

                    // Get the file instance.
                    $fileinstance = $fs->get_file(
                        $module->context->id,
                        $component,
                        'content',
                        0,
                        $modulefile['filepath'],
                        $modulefile['filename']
                    );

                    if (!$fileinstance) {
                        continue;
                    }

                    // Generate the Amanote resource id for the file.
                    $amaresourceid = $this->generate_ama_resource_id($course->id, $modid, $fileinstance->get_id());

                    // Add the file to the array of files.
                    array_push($files, [
                        'module' => [
                            'id' => $module->id,
                            'contextId' => $module->context->id,
                            'name' => $module->name,
                            'component' => $component,
                        ],
                        'id' => $fileinstance->get_id(),
                        'path' => $modulefile['filepath'],
                        'name' => $modulefile['filename'],
                        'mimetype' => $modulefile['mimetype'],
                        'url' => $modulefile['fileurl'],
                        'amaResourceId' => $amaresourceid,
                    ]);
                }
            }
        }

        return $files;
    }

    /**
     * Generate an Amanote resource id.
     *
     * @param int $courseid
     * @param int $modid
     * @param int $fileid
     *
     * @return string The generated resource id.
     */
    private function generate_ama_resource_id($courseid, $modid, $fileid) {
        return $courseid . '.' . $modid . '.' . $fileid;
    }

    /**
     * Return all the files in resource and folder activities for a given course.
     *
     * @param course $course - The course.
     *
     * @return array Array of files.
     */
    private function get_course_files($course) {
        return array_merge(
            $this->get_files_by_module($course, 'mod_resource'),
            $this->get_files_by_module($course, 'mod_folder')
        );
    }

    /**
     * Filters the given HTML text.
     *
     * @param string $text HTML to be processed.
     * @param array $options
     *
     * @return string String containing processed HTML.
     */
    public function filter($text, array $options = array()) {
        return $text;
    }
}
