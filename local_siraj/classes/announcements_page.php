<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_siraj;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Siraj Announcements: the site announcements forum (Moodle's "Site announcements", on the front page) as a list of
 * cards and a reading page. Moodle's forum still does the posting and editing; this only shows what the person may see
 * (forum_get_discussions applies group, timed-post and visibility rules).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class announcements_page {
    /** @var int Announcements newer than this many days carry a "New" chip. */
    const NEWDAYS = 7;

    /** @var \stdClass|null The site announcements forum. */
    protected $forum = null;
    /** @var \cm_info|null */
    protected $cm = null;
    /** @var \context_module|null */
    protected $context = null;

    /**
     * Find the forum. Nothing is created: a site without announcements just shows the empty state.
     */
    public function __construct() {
        global $DB;
        $forum = $DB->get_record('forum', ['course' => SITEID, 'type' => 'news'], '*', IGNORE_MULTIPLE);
        if (!$forum) {
            return;
        }
        $cm = get_fast_modinfo(SITEID)->get_instances_of('forum')[$forum->id] ?? null;
        if (!$cm || !$cm->uservisible || !has_capability('mod/forum:viewdiscussion', \context_module::instance($cm->id))) {
            return;
        }
        $this->forum = $forum;
        $this->cm = $cm;
        $this->context = \context_module::instance($cm->id);
    }

    /**
     * The site announcements forum, if the person can read it.
     *
     * @return \stdClass|null
     */
    public function forum(): ?\stdClass {
        return $this->forum;
    }

    /**
     * Discussions the person can see, pinned first, then newest.
     *
     * @return \stdClass[] First posts, keyed by discussion id.
     */
    protected function discussions(): array {
        if (!$this->forum) {
            return [];
        }
        $out = [];
        foreach (forum_get_discussions($this->cm, 'd.pinned DESC, d.timemodified DESC', true) as $post) {
            $out[$post->discussion] = $post;
        }
        return $out;
    }

    /**
     * The page: the list, or one announcement when $id is given.
     *
     * @param int $id Discussion id, or 0 for the list.
     * @return array Template context.
     */
    public function export(int $id = 0): array {
        global $DB, $USER, $PAGE;
        $posts = $this->discussions();
        if ($id && !isset($posts[$id])) {
            throw new \moodle_exception('invaliddiscussionid', 'forum');
        }
        $users = $posts ? $DB->get_records_list('user', 'id', array_unique(array_column($posts, 'userid'))) : [];
        $replies = $this->forum ? forum_count_discussion_replies($this->forum->id) : [];
        $canpost = $this->context && has_capability('mod/forum:addnews', $this->context);
        $items = [];
        foreach ($posts as $did => $post) {
            $items[] = $this->item($post, $users[$post->userid] ?? null, (int)($replies[$did]->replies ?? 0));
        }
        $data = [
            'hasitems' => (bool)$items,
            'canpost' => $canpost,
            'posturl' => $canpost ? (new \moodle_url('/mod/forum/post.php', ['forum' => $this->forum->id]))->out(false) : '',
            'listurl' => (new \moodle_url('/local/siraj/announcements.php'))->out(false),
            'icon_plus' => \theme_siraj\icons::svg('plus'),
            'icon_back' => \theme_siraj\icons::svg('arrow-left'),
            'icon_arrow' => \theme_siraj\icons::svg('arrow-right'),
            'icon_megaphone' => \theme_siraj\icons::svg('megaphone'),
            'icon_file' => \theme_siraj\icons::svg('file'),
            'icon_download' => \theme_siraj\icons::svg('download'),
            'icon_message' => \theme_siraj\icons::svg('message'),
            'icon_pencil' => \theme_siraj\icons::svg('pencil'),
            'icon_external' => \theme_siraj\icons::svg('external'),
        ];
        if (!$id) {
            $data['items'] = $items;
            $data['count'] = count($items);
            return $data;
        }
        // One announcement: the full post, its files, and the ones before and after it.
        $ids = array_keys($posts);
        $at = array_search($id, $ids);
        $post = $posts[$id];
        $item = $items[$at];
        $message = file_rewrite_pluginfile_urls($post->message, 'pluginfile.php', $this->context->id, 'mod_forum', 'post', $post->id);
        $item['body'] = format_text($message, $post->messageformat, ['context' => $this->context]);
        $item['readtime'] = max(1, (int)round(str_word_count(html_to_text($item['body'], 0, false)) / 200));
        $files = [];
        foreach (get_file_storage()->get_area_files($this->context->id, 'mod_forum', 'attachment', $post->id, 'filename', false) as $f) {
            $files[] = ['name' => $f->get_filename(), 'size' => display_size($f->get_filesize()),
                'url' => \moodle_url::make_pluginfile_url($this->context->id, 'mod_forum', 'attachment', $post->id, '/',
                    $f->get_filename(), true)->out(false)];
        }
        $item['files'] = $files;
        $item['hasfiles'] = (bool)$files;
        // Moodle's discussion page for replies and staff tools (siraj=classic keeps it from coming back here).
        $item['forumurl'] = (new \moodle_url('/mod/forum/discuss.php', ['d' => $id, 'siraj' => 'classic']))->out(false);
        $canedit = ($post->userid == $USER->id && has_capability('mod/forum:replypost', $this->context))
            || has_capability('mod/forum:editanypost', $this->context);
        $item['editurl'] = $canedit ? (new \moodle_url('/mod/forum/post.php', ['edit' => $post->id]))->out(false) : '';
        $item['canreply'] = has_capability('mod/forum:replynews', $this->context);
        $data['single'] = $item;
        $data['prev'] = $at > 0 ? $items[$at - 1] : null;
        $data['next'] = $at < count($items) - 1 ? $items[$at + 1] : null;
        $PAGE->set_title($item['subject']);
        // Read tracking, where the forum has it on.
        if (forum_tp_can_track_forums($this->forum) && forum_tp_is_tracked($this->forum)) {
            forum_tp_add_read_record($USER->id, $post->id);
        }
        return $data;
    }

    /**
     * One announcement as a card.
     *
     * @param \stdClass $post First post of the discussion.
     * @param \stdClass|null $author
     * @param int $replies
     * @return array
     */
    protected function item(\stdClass $post, ?\stdClass $author, int $replies): array {
        global $PAGE, $USER;
        $text = html_to_text(format_text($post->message, $post->messageformat, ['context' => $this->context]), 0, false);
        $when = max((int)$post->created, (int)($post->timestart ?? 0));
        return [
            'id' => (int)$post->discussion,
            'url' => (new \moodle_url('/local/siraj/announcements.php', ['id' => $post->discussion]))->out(false),
            'subject' => format_string($post->subject, true, ['context' => $this->context]),
            'excerpt' => shorten_text(trim(preg_replace('/\s+/u', ' ', $text)), 190),
            'author' => $author ? fullname($author) : '',
            'avatar' => $author ? (new \user_picture($author))->get_url($PAGE)->out(false) : '',
            'date' => userdate($when, get_string('strftimedate', 'langconfig')),
            'day' => userdate($when, '%d'),
            'month' => userdate($when, '%b'),
            'iso' => date('c', $when),
            'isnew' => $when > time() - self::NEWDAYS * DAYSECS && $post->userid != $USER->id,
            'pinned' => !empty($post->pinned),
            'replies' => $replies,
            'hasreplies' => $replies > 0,
            'attachments' => !empty($post->attachment),
        ];
    }
}
