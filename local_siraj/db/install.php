<?php
/**
 * Install: the Siraj course fields, the Line manager role and the Manager / Job role profile fields.
 * Visitors who are not signed in go straight to the sign-in page (Moodle's site home is not a Siraj landing page);
 * the certificate check stays public.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Create the course fields, role and profile fields if they don't exist.
 *
 * @return bool
 */
function xmldb_local_siraj_install() {
    \local_siraj\coursefields::ensure();
    \local_siraj\setup::ensure();
    set_config('forcelogin', 1);
    set_config('guestloginbutton', 0);
    return true;
}
