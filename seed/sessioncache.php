<?php
// Local lab: keep Moodle's session-mode caches in a file store instead of the PHP session, which Moodle requires
// before read-only sessions can be enabled ($CFG->enable_read_only_sessions). On a real server Redis is the usual store.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
$writer = \core_cache\config_writer::instance();
$config = \core_cache\config::instance();
if (!array_key_exists('sirajsessionfile', $config->get_all_stores())) {
    $writer->add_store_instance('sirajsessionfile', 'file', ['path' => $CFG->dataroot . '/sirajsessioncache', 'autocreate' => 1]);
}
$writer->set_mode_mappings([
    \core_cache\store::MODE_APPLICATION => ['default_application'],
    \core_cache\store::MODE_SESSION => ['sirajsessionfile'],
    \core_cache\store::MODE_REQUEST => ['default_request'],
]);
echo "session caches -> sirajsessionfile\n";
