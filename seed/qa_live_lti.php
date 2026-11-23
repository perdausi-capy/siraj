<?php
// Adds a BigBlueButton room and an external tool (LTI) to the SIRAJ-QA course, for checking their Siraj views.
// BigBlueButton uses Blindside Networks' public test server and the LTI tool is saLTIre's public test tool
// (https://saltire.lti.app/tool); both credentials are published test values, not secrets. No names or emails are
// sent to the test tool. Dummy content only. Safe to run again (skips what exists).
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
\core\session\manager::set_user(get_admin());
$course = $DB->get_record('course', ['shortname' => 'SIRAJ-QA'], '*', MUST_EXIST);
$gen = phpunit_util::get_data_generator();
$c = ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL];

// BigBlueButton is off by default in Moodle; turn it on with the public test server.
$DB->set_field('modules', 'visible', 1, ['name' => 'bigbluebuttonbn']);
if (!get_config('core', 'bigbluebuttonbn_server_url')) {
    set_config('bigbluebuttonbn_server_url', \mod_bigbluebuttonbn\local\config::DEFAULT_SERVER_URL);
    set_config('bigbluebuttonbn_shared_secret', \mod_bigbluebuttonbn\local\config::DEFAULT_SHARED_SECRET);
}
set_config('bigbluebuttonbn_default_dpa_accepted', 1);
if (!$DB->record_exists('bigbluebuttonbn', ['course' => $course->id])) {
    $gen->create_module('bigbluebuttonbn', $c + ['name' => 'Weekly live Q&A', 'type' => 0,
        'intro' => '<p>Bring your questions about this week\'s lessons. Join from your laptop or phone.</p>',
        'openingtime' => time() - HOURSECS, 'closingtime' => time() + 30 * DAYSECS, 'wait' => 0]);
    echo "BigBlueButton room added\n";
}

// A site-wide external tool, so teachers can pick it from the activity chooser like a real provider.
$typeid = $DB->get_field('lti_types', 'id', ['baseurl' => 'https://saltire.lti.app/tool']);
if (!$typeid) {
    $type = (object)['name' => 'LTI test tool (saLTIre)', 'baseurl' => 'https://saltire.lti.app/tool',
        'description' => 'Public LTI test tool, for checking launches.', 'state' => LTI_TOOL_STATE_CONFIGURED,
        'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER, 'ltiversion' => LTI_VERSION_1];
    $config = (object)['lti_resourcekey' => 'jisc.ac.uk', 'lti_password' => 'secret', 'lti_sendname' => LTI_SETTING_NEVER,
        'lti_sendemailaddr' => LTI_SETTING_NEVER, 'lti_acceptgrades' => LTI_SETTING_ALWAYS,
        'lti_launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS, 'lti_coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER];
    $typeid = lti_add_type($type, $config);
    echo "LTI tool type $typeid added\n";
}
if (!$DB->record_exists('lti', ['course' => $course->id])) {
    $gen->create_module('lti', $c + ['name' => 'Partner simulation', 'typeid' => $typeid, 'toolurl' => '',
        'resourcekey' => '', 'password' => '', 'instructorchoicesendname' => 0, 'instructorchoicesendemailaddr' => 0,
        'launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS,
        'intro' => '<p>An interactive exercise hosted by an outside provider. It opens right here.</p>']);
    echo "LTI activity added\n";
}
rebuild_course_cache($course->id, true);
foreach (get_fast_modinfo($course)->get_cms() as $cm) {
    if (in_array($cm->modname, ['bigbluebuttonbn', 'lti'])) {
        echo "$cm->id $cm->modname\n";
    }
}
