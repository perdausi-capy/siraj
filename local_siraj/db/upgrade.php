<?php
/**
 * Upgrade steps for local_siraj.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_siraj_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026111700) {
        // Phase 2: compliance rules, manager notes and reminders, issued certificates.
        foreach (['local_siraj_rolereq', 'local_siraj_note', 'local_siraj_cert'] as $name) {
            if (!$dbman->table_exists($name)) {
                $dbman->install_one_table_from_xmldb_file(__DIR__ . '/install.xml', $name);
            }
        }
        \local_siraj\setup::ensure();
        upgrade_plugin_savepoint(true, 2026111700, 'local', 'siraj');
    }
    return true;
}
