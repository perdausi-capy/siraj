<?php
// A client-side manager for demos: Rania Khoury, L&D manager in HR, with Moodle's Manager role on the whole site.
// That is how a client's own admins are meant to work: Team, Reports, Studio, the compliance matrix, people and
// courses, without full site administration (which stays with Capytech, along with Moodle's own wording).
// Run: php siraj_seed/manager.php <password>
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');

use local_siraj\setup;

$pass = $argv[1] ?? die("password arg required\n");
\core\session\manager::set_user(get_admin());
setup::ensure();
$user = $DB->get_record('user', ['username' => 'rania']) ?: phpunit_util::get_data_generator()->create_user([
    'username' => 'rania', 'password' => $pass, 'firstname' => 'Rania', 'lastname' => 'Khoury',
    'email' => 'rania@example.com', 'city' => 'Dubai', 'country' => 'AE']);
$DB->set_field('user', 'department', 'HR', ['id' => $user->id]);
setup::set_field(setup::FIELD_JOBROLE, $user->id, 'L&D manager');
role_assign($DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST), $user->id, context_system::instance()->id);
// She is a learner too: the welcome course, like everyone.
if ($onb = $DB->get_field('course', 'id', ['shortname' => 'ONB101'])) {
    phpunit_util::get_data_generator()->enrol_user($user->id, $onb, 'student');
}
echo "manager: rania (Manager role, site-wide)\n";
