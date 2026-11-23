<?php
// Siraj default dashboard (shown under the Siraj sections on Home): Timeline, Upcoming events, Latest announcements.
// Home already has next step, "Pick up where you left off", recommendations and achievements, so no course blocks.
// Adds two demo site announcements so the announcements block isn't empty. Resets everyone's dashboard to this default.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once($CFG->libdir . '/testing/generator/lib.php');

$page = my_get_page(null, MY_PAGE_PRIVATE);
$ctx = context_system::instance();
$wanted = ['timeline' => 0, 'calendar_upcoming' => 1, 'news_items' => 2];
$blocks = $DB->get_records('block_instances', ['parentcontextid' => $ctx->id, 'pagetypepattern' => 'my-index', 'subpagepattern' => $page->id]);
foreach ($blocks as $b) {
    if (!isset($wanted[$b->blockname]) || !empty($done[$b->blockname])) {
        blocks_delete_instance($b);
        continue;
    }
    $done[$b->blockname] = true;
    $DB->update_record('block_instances', (object)['id' => $b->id, 'defaultregion' => 'content', 'defaultweight' => $wanted[$b->blockname]]);
}
foreach ($wanted as $name => $weight) {
    if (empty($done[$name])) {
        $DB->insert_record('block_instances', (object)['blockname' => $name, 'parentcontextid' => $ctx->id,
            'showinsubcontexts' => 0, 'requiredbytheme' => 0, 'pagetypepattern' => 'my-index', 'subpagepattern' => $page->id,
            'defaultregion' => 'content', 'defaultweight' => $weight, 'configdata' => '', 'timecreated' => time(), 'timemodified' => time()]);
    }
}

// Demo announcements in the site news forum.
$news = forum_get_course_forum(SITEID, 'news');
if (!$DB->record_exists('forum_discussions', ['forum' => $news->id])) {
    $admin = get_admin();
    $gen = new testing_data_generator();
    $posts = [
        ['Welcome to Siraj Learning', '<p>Your learning space is ready. Start with your next step on Home, and find the full library under Explore. Questions? Reply here or contact the L&amp;D team.</p>'],
        ['Annual compliance training is open', '<p>This year\'s mandatory courses are now assigned: Information Security, Workplace Health &amp; Safety and Code of Conduct. Please complete them before 31 December.</p>'],
    ];
    foreach ($posts as $i => [$subject, $message]) {
        $gen->get_plugin_generator('mod_forum')->create_discussion(['course' => SITEID, 'forum' => $news->id,
            'userid' => $admin->id, 'name' => $subject, 'message' => $message, 'messageformat' => FORMAT_HTML,
            'timemodified' => time() - (count($posts) - $i) * 3600]);
    }
}

my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index');
echo "dashboard: timeline, upcoming events, announcements\n";
