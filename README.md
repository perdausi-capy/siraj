# Siraj local Moodle (theme lab)

Fresh local Moodle 5.0.11 (same version as the live client sites) for building and previewing `theme_siraj`.
Official moodle-docker setup, MariaDB, PHP 8.3, bound to 127.0.0.1:8000 only. Dummy data only, no client data.
Set up 10 Nov 2026. Nothing here touches any server.

## Run it
- Docker must be running: `sudo systemctl start docker` (Perdausi runs sudo in his own terminal).
- Start: `. ~/siraj-local/env.sh && ~/siraj-local/moodle-docker/bin/moodle-docker-compose up -d`
- Stop: same with `stop` (keeps data). `down -v` wipes the database.
- Open http://localhost:8000. Logins: `admin`, and dummy learner `layla` (also omar = teacher, sara, yousef, mariam, adam).
  The password is in `~/.config/siraj-local/admin-pass` (local only; don't reuse it anywhere).

## What's here
- `moodle/`: Moodle core (git, MOODLE_500_STABLE). `config.php` is the moodle-docker template with debug output turned off.
- `theme_siraj/` (v0.2, phase 1): **the Siraj app shell**, a Boost child theme. Installed as `moodle/theme/siraj`.
  - `layout/drawers.php` + `templates/drawers.mustache`: every normal page sits inside the shell: left sidebar
    (`sidebar`), top bar (`topbar`: trail, Ctrl+K search, light/dark, notifications, user menu), phone tab bar
    (`mobilebar`), command palette (`palette`), slim footer. Boost's page logic is reused unchanged, so course index,
    blocks, editing and activity pages keep working.
  - `classes/output/shell.php`: sidebar items and the page title. `classes/icons.php`: the one icon set (Lucide paths) + flame.
  - `layout/login.php` + `templates/login.mustache` + `templates/core/loginform.mustache`: the sign-in page.
  - `scss/siraj/`: `tokens` (light/dark colours as CSS variables), `shell`, `components` (Moodle buttons, cards, forms,
    course page, dark-mode fixes), `login`, `pages` (Siraj pages). Brand values in `scss/preset/siraj.scss`.
  - Dark mode = Bootstrap's `data-bs-theme`, saved in the browser (localStorage), applied before the page paints.
  - The first, colours-only version is kept in `theme_siraj.v0-backup/` (SSH preset lives there for now).
- `local_siraj/` (v0.1): **Siraj pages**, installed as `moodle/local/siraj`. Phase 1 = Home (`/local/siraj/index.php`),
  set as the site start page. `classes/home.php` builds it from standard Moodle data only: enrolments, activity
  completion, calendar due dates, `user_lastaccess`, the standard log (weekly rhythm), and two course custom fields it
  creates on install (`siraj_mandatory`, `siraj_duration`, category "Siraj"). Weekly goal is a plugin setting.
  Phase 2 = course overview (`course.php`, `classes/course_page.php`: cover, progress ring, learning path from sections)
  and Learning mode (`learn.php`, `classes/player.php`: page/url/file play inside and are logged as viewed like Moodle's
  own view pages; other types get a launch card to Moodle's page; manual "Mark as done" with sesskey; prev/next).
  `classes/progress.php` = shared course/completion logic. Learners opening `/course/view.php` are redirected to the
  overview (setting `local_siraj/courseoverview`); teachers/admins, editing mode and `?siraj=classic` keep Moodle's page.
  Phase 3 = My learning (`learning.php`: list + agenda view, tabs/filters/search run in the browser, no reload),
  Explore (`explore.php`: featured collection, category rows of course tiles, instant search), course preview for
  people not enrolled (outline + "Enrol and start" via Moodle's `enrol_self` with its own permission checks; keyed or
  other enrolment methods fall back to Moodle's enrolment page), quiz pages restyled (one question per page).
  Phase 4 = Achievements (`achievements.php`: certificate wallet, badges, milestones from completion + logs, next
  achievements), printable certificate (`certificate.php`, only when every tracked activity is complete; Print/Save as
  PDF, A4 landscape; ID = hash of user+course+completion time+site), Inbox (`inbox.php`: Moodle notifications by day,
  instant tabs, open/mark read/mark all read through `core_message\api`), quiz summary/review restyled. Moodle's
  notifications page and My badges redirect learners to Inbox/Achievements (`?siraj=classic` bypasses).
  `seed/notifications.php` adds dummy notifications + a badge.
  Learner-side finish: Siraj profile (`profile.php`, own profile only; others stay Moodle's), quiz results card
  (`theme_siraj/classes/output/mod_quiz_renderer.php`, theme override of Moodle's quiz review page; grades unchanged),
  calendar + preferences restyled. Drawer slide animations are off until the page has loaded (`siraj-ready`) to
  avoid layout shift; `html, body { overflow-x: clip }` stops closed off-screen drawers widening the page.
  Prefetch never includes course.php/learn.php (they record views and last access).
  **Phase 2 (manager and admin dashboards)**, reusing Waymark's rules on standard Moodle (no IOMAD):
  - Who reports to whom (`classes/setup.php`): the "Line manager" role assigned in a person's user context, or the
    "Manager" profile field holding the manager's username/email (fillable with Upload users). Department = Moodle's own
    field; job role = "Job role" profile field. Capabilities: `local/siraj:viewteam` (via the role), `viewreports` and
    `managecompliance` (site Manager role by default).
  - My team (`team.php`): KPIs, people worst first with Waymark's status rules (`classes/team.php`: overdue, at risk,
    not started, on track, done, judged on mandatory courses), instant filters/search, bulk reminders (message provider
    `reminder`; skips people reminded in the last day), status ring, by course, deadlines.
  - Person page (`person.php`): courses, certificates with codes, 12-week rhythm, notes and reminder history.
  - Reports (`reports.php`, `classes/reports.php`): team or everyone/department, KPIs, weekly pace chart, by department,
    by course, people, CSV and Excel export (Moodle dataformat). Above 400 people asks for a department.
  - Compliance (`matrix.php`, `classes/matrix.php`): required courses per job role (`local_siraj_rolereq`), gaps,
    enrol the missing in one click, rules editor.
  - Admin studio (`studio.php`, `classes/studio.php`): site-wide numbers from aggregate SQL, 12-week trend, course health
    (completion, drop-off activity, flags: no tracking, low completion, quiet), admin shortcuts.
  - Certificate check (`verify.php`, public; `classes/certs.php`, `local_siraj_cert`).
  - Charts: `amd/src/charts.js` wraps Moodle's own Chart.js (no extra library), drawn when scrolled into view, follows
    dark mode. Small charts (status ring, bars) are plain CSS.
  - Speed: another person's progress is read in one query, and cached per person (`db/caches.php`), cleared by
    observers (`db/events.php`) when completion, enrolment or a course changes. My team for 12 people: ~2 ms warm.
  - Styles: `theme_siraj/scss/siraj/dashboards.scss`. Sidebar "Management" items come from `local_siraj\nav`.
  **Learning mode for book, SCORM and H5P**: books play natively (chapter list, previous/next chapter; reaching the
  last chapter completes "view" completion via Moodle's `book_view`); SCORM runs in Moodle's own player
  (`mod/scorm/player.php`, popup display) framed inside Learning mode, so SCORM tracking stays Moodle's (a frame
  that navigates away, e.g. SCORM's exit, takes the whole window there; full-screen button); H5P uses Moodle's
  `core_h5p\player::display` with mod_h5pactivity xAPI tracking. Lesson text is `dir="auto"` so English content reads
  correctly inside the Arabic interface. Demo items in Project Management Fundamentals (`seed/content.php`);
  test: `tools/play9.js <bookcmid> <scormcmid> <h5pcmid>` (45 46 47 locally).
  **Speed: no more queueing behind the session lock.** Moodle locks a user's session per request, so a click waited
  for anything still loading (a SCORM/H5P player, a prefetch). Siraj view pages now open the session read-only
  (`local_siraj/readonly.php`, included before config.php); a page with a waiting redirect message takes the lock
  once to clear it (hook). Needs in config.php: `$CFG->enable_read_only_sessions = true;` and a session handler that
  supports it (file sessions don't): locally `\core\session\database`, on real servers Redis. Session-mode caches
  must live outside the session (`seed/sessioncache.php` maps them to a file store; Redis on servers), and existing
  sessions are cleared once when switching (everyone signs in again).
  **Learners never see Moodle's activity pages for supported types**: page/url/resource/book/H5P/assignment/quiz/SCORM
  view pages open Learning mode (hook; teachers and `?siraj=classic` keep Moodle's). Assignment and quiz steps show
  their status, rules and one next action (`player::assign_status`, `quiz_status`). Pages that must stay Moodle's
  (submitting, quiz attempts, grades, account settings, calendar) get `body.siraj-moodlepage`: no course index, a
  "Back to …" link, Siraj header, restyled forms/status tables/calendar cards (end of polish.scss).
  Moodle's course list/search open Explore (with the search term).
  **Top bar**: language switcher (globe, saves the user's language: `local/siraj/lang.php`), notification popover in
  the Siraj style (`templates/message_popup/notification_content_item.mustache`), focus ring only for keyboard use.
  Light/dark and the collapsed sidebar are stored in cookies, because Moodle clears localStorage on every JS
  version change (each upgrade/purge), which reset everyone's choice.
  Demo: `seed/realistic.php` (assignment instructions + PDF/Word upload + completion on submit, quiz text, Siraj SCORM
  lesson `seed/scorm-kickoff.zip` that resumes without a prompt). Audit: `tools/audit10.js <user> <tag>` screenshots
  every Moodle page a learner can reach into `screenshots/audit/`.
  **Teaching** (`teaching.php`, `classes/teaching_page.php`): the teacher's home. Courses taught with learner status
  (from the cached per-person progress), grading queue (assignment submissions needing grading, quiz attempts needing
  manual marks), due in 14 days, one-click tools. Home opens Teaching for people who only teach; learner pages count
  only courses where the person learns (`moodle/course:isincompletionreports`); in a taught course the sidebar
  highlights Teaching. Teacher demo account: `hana` (`seed/teacher.php`).
  **Teacher/admin Moodle pages**: grading status as coloured text, grader actions in Siraj colours, Moodle's sticky
  action bar beside the sidebar with room reserved at every size, wide tables (gradebook, submissions, roles, tasks)
  scroll in their own box (the page itself never scrolls sideways), filter bars wrap, admin pages show their real title
  instead of the site name, Edit mode label hidden on tablets, compact top bar on phones.
  Tests: `tools/audit11.js` (teacher pages), `tools/audit12.js <user> <tag> <width>` (admin pages).
  **Arabic and right-to-left**: Moodle's Arabic language pack (in moodledata/lang/ar, from download.moodle.org) plus our
  own `lang/ar` files for theme_siraj and local_siraj (machine-assisted first version: a native speaker must review before
  clients see it; admin-only settings stay English). Moodle mirrors the compiled CSS with RTLCSS for RTL languages, so
  **never write hand-made mirrored rules** (they get flipped back) and avoid `inset:` shorthand for one-sided positions
  (RTLCSS can't flip it). RTLCSS does not mirror rotations; arrow icons are mirrored by `si-dir` (icons::DIRECTIONAL).
  Direction-neutral RTL rules live at the end of `polish.scss` (Arabic font, no letter spacing, codes kept LTR, English
  names and lesson text get `unicode-bidi: plaintext`). Reminders are written in the learner's language.
  Test users layla and omar are set to Arabic (`lang` = ar); `tools/shoot8.js` takes RTL screenshots into `screenshots/rtl/`.
- `seed/`: dummy data (`seed.php`; `team.php` = Omar's team of 12, two extra mandatory courses, job roles, rules), course field values + start page (`fields.php`), preset/logo switcher (`preset.php`),
  dashboard baseline (`dashboard.php`), admin as a learner with progress (`adminlearn.php`), siraj5 leftovers removed (`clean_siraj5.php`), QA course with every activity type (`qa_activities.php`, hidden SIRAJ-QA), free siraj5 courses (`import_siraj5.php <folder>`: 3 Wellness + 6 Business Essentials from `import/siraj5/*.mbz`, backed up from siraj5 with no user data; customcert and zoom activities are skipped because those plugins are not installed), stock-photo course covers (`photos.php`, CC0 from StockSnap, list in `img/photo/CREDITS.txt`; the Home next-step banner and Explore featured banner use the course photo), wording preview (`lang/en_local`), cover images. Copied into `moodle/siraj_seed/` to run.
- `design/`: my first mockup (`index.html`), the prompt (`PROMPT.md`), and the chosen prototype (`proto/`).
- `tools/`: `darkaudit.js` (dark mode: light surfaces + low-contrast text on ~30 admin/teacher/learner pages and popups),
  `soak.js` (5-minute realistic browsing, reports slow loads/5xx), `perf.js` + `perf2.js` (speed, page weight, 11 screen sizes) + `layoutcheck.js` (real scrollbars: no sideways scroll, fixed top bar, footer at bottom, content width); run all at the end of every phase, `shoot2.js` /
  `shoot3.js` (phase 1 / 2 screenshots + flow checks), `shoot7.js` + `flow7.js` + `cls7.js` (dashboards: screenshots,
  reminders/notes/export/rules/enrol/verify and access checks, layout shift). `layoutcheck.js` and `soak.js` take
  `SIRAJ_USER` and `PAGES` / `SOAK_URLS`; `perf.js` takes `PAGES`, `prefetch.js`, Playwright.
- `screenshots/phase1/`: current screenshots. `screenshots/index.html` is the old v0 comparison.

## Demo accounts (all dummy, password in ~/.config/siraj-local/admin-pass)
| Account | Role | Language | Shows |
|---|---|---|---|
| `layla` | Learner (Engineering) | English | Home, My learning, Learning mode, Achievements, Inbox |
| `mariam` | Learner (HR) | Arabic | The same in Arabic, plus the two Arabic courses |
| `sara` | Learner | English | Book / SCORM / H5P / assignment / quiz in Project Management |
| `omar` | Line manager of 12 | Arabic | My team, person page, Reports, Compliance |
| `hana` | Teacher (Project Management, Health & Safety, Arabic courses) | English | Teaching, grading |
| `admin` | Site admin | English | Admin studio, all reports, compliance rules, site admin |

Content: `seed/democontent.php` (real lesson text, outcomes and quiz questions for the 8 courses, covers, two courses
in Arabic: AR-HSE, AR-CS). Order of seeds: seed.php, fields.php, team.php, teacher.php, content.php, realistic.php,
democontent.php (each skips what already exists).

## Update the theme after editing
```
cd ~/siraj-local && ./deploy.sh
```
`deploy.sh` copies the plugins to a staging folder and swaps them in with an atomic rename (never delete-then-copy:
requests during that gap got 500s and hung), runs the upgrade only when a `version.php` changed, then warms the theme
CSS/JS and the main pages. Don't purge caches or redeploy while someone is testing unless needed.
Template comments: an example JSON ending in `}}` closes the Mustache comment early and prints `}}` on the page; write `} }`.
Bump `$plugin->version` in `theme_siraj/version.php` when settings or hooks change.

## Home = dashboard (16 Nov 2026)
Siraj Home is also the person's Moodle dashboard (page type `my-index`): Edit mode adds blocks under the Siraj sections
("Your dashboard"), with "Reset page to default". Until someone edits, they see the site default, which admins edit at
Site administration > Appearance > Default Dashboard page (`/my/indexsys.php`, linked from Home in Edit mode).
`/my/` opens Home. Siraj default = Timeline only (`seed/dashboard.php`). Blocks are styled in `scss/siraj/blocks.scss`
(icons per block: `icons::BLOCKS`, emitted as CSS by `icons::block_icon_css()`); the "Add a block" picker is the theme
override `templates/core/add_block_body.mustache` (cards in a grid). Edit mode takes a writable session (the block
list lives in the session); plain viewing stays read-only.

## Staging server
https://sirajv2-staging.capy-dev.com. Server details, access and backups are in `STAGING.local.md` (kept out of git).
Deploy with `./deploy-staging.sh`.

## Not done yet
- Native review of the Arabic strings, Arabic course content/demo data, AI tutor wiring, privacy API provider for
  notes/reminders, indirect reports (manager of managers), Waymark later options (ask bar, skills, renewals, policy).
- Moodle's own pages (admin, reports, grader) are only restyled, not redesigned; dark mode on them is best-effort.
- No SSH preset: SSH's live site stays as it is (decided 16 Nov 2026). The new Siraj is for future clients.
- Git repo for theme_siraj + local_siraj (location not decided).

## Learning mode: activity views (17 Nov 2026)
`local_siraj/classes/activity_views.php`. Native Siraj views (templates `learn_feedback|forum|glossary|choice|folder|live`):
feedback (one-page questionnaire, saved via `mod_feedback_completion`), forum (topics, thread, reply, new topic via
`forum_add_new_post` / `forum_add_discussion` + events + completion), glossary (search, A-Z; a glossary with no entries
is shown as a page), choice (`choice_user_submit_response`, results bars), folder (file list), BigBlueButton (times +
join), external tool (staged launch). Lesson, wiki, workshop, database, IMS package run their Moodle pages in the stage
iframe; the theme adds `html.siraj-inframe` inside frames and hides its shell (`shell.scss`). Frames that leave the
activity's `/mod/<name>/` path take the whole window. SCORM: Moodle's TOC hidden as soon as the player page exists.

## Concept A design (18 Nov 2026)
`theme_siraj/scss/siraj/concepta.scss` (loaded last; drop 'concepta' from the parts list in lib.php to revert): Inter (UI)
+ Instrument Serif (greetings, page titles, heroes), both self-hosted in `fonts/`; navy palette, pill buttons, navy
sidebar in both themes, softer shadows. Home (`home.mustache` + `home.php`): hero carousel (next step, due soon,
recommendation), stat strip, photo cards, right rail (weekly rhythm, Siraj AI, coming up, recently completed).
Static previews of the concepts: `design-concepts/` (`build.py`, `build_a.py`).
Messages drawer: styled in `concepta.scss` (`.message-app`), composer template override
`theme_siraj/templates/core_message/message_drawer_view_conversation_footer_content.mustache` (one-line composer, buttons
in a row). Demo chats for layla: `seed/messages.php`. Login concepts: `design-concepts/login-1..4.html` (`build_login.py`).
Siraj chat (19 Nov 2026): `theme_siraj/templates/chat.mustache` (panel + JS on Moodle's core_message web services:
get_conversations, get_conversation_messages, send_messages_to_conversation / send_instant_messages, mark read,
favourites, user search). Moodle's message drawer is switched off with `$CFG->hooks_callback_overrides` in config.php
(local and staging; staging backup `config.php.bak-20261118`). The chat answers Moodle's message-drawer pubsub events and
calls `message_drawer_helper.markDrawerReady()`, so Moodle "Message" buttons open it. Needs the same config line on any
new server.
Full page: `local_siraj/messages.php` (same chat in page mode, with Settings: who can message you, email when offline, Enter to send). /message/index.php and your own /message/edit.php redirect there.
Sign-in page (18 Nov 2026): concept 2 with a looping video background (`theme_siraj/video/siraj-dubai-sunrise.*`,
Pexels free licence, credits in `video/CREDITS.txt`), headline left, Moodle's login form in a glass card. The video loads
only on screens 768px+ without reduced motion or data saver; otherwise the poster image shows.

## Speed + screen check (19 Nov 2026, staging)
Desktop, cold: all pages FCP 0.5-1.4 s (Explore had a 16 s load: imported 0.85-1.2 MB PNG covers; now auto-shrunk to
1200 px JPEG by `local_siraj\covers` on course save + `seed/optimise_covers.php`). Old DM Sans / Plus Jakarta fonts
removed (were still downloaded), preloads point at Inter + Instrument Serif. Home: only the first hero photo loads up
front; card photos lazy-load. Slow 4G + 4x CPU, Home first visit: FCP ~3.9 s, LCP ~7 s (was LCP 7.7 s); cached 1.2-1.7 s.
11 screen sizes x 16 pages: no overflow (fixed Home rhythm card + Coming up at 320 px, Achievements stats at 320-360 px;
Inbox tabs scroll sideways by design). Tools: `tools/perf3.js` (all), `perf4g.js`, `resp3.js`.
Arabic review (19 Nov 2026): all strings translated (20 settings/capability strings added). Counts use the
"label: number" form (الردود: 12) so they are correct for any number. Number forms (20 Nov 2026):
`local_siraj\plural::str($key, $n)` picks `$key_one|two|few|many|other` by CLDR rules (Arabic: 1, 2, 3-10, 11-99, other),
so day phrases, minutes, word limits and the weekly goal read correctly for any number. Every form key must also
exist in English (Moodle only knows keys English has). Full read-through of all 694 Arabic strings done the same day;
counts on people use "label: number"; weekday letters ن ث ر خ ج س ح; tanween written ًا throughout.
Siraj Grades: `local_siraj/grades.php` + `classes/grades_page.php` (gradebook data, per-learner max for natural
aggregation); Moodle's grades overview and own user report redirect there.

## Live sessions + external tools (20 Nov 2026)
`seed/qa_live_lti.php` adds a BigBlueButton room and an LTI 1.1 activity to SIRAJ-QA. BigBlueButton is off by default in
Moodle; the seed turns it on with Blindside's public test server (constants in `mod_bigbluebuttonbn\local\config`; Moodle
warns "credentials will soon expire" until a real server is set). The LTI tool is saLTIre's public test tool
(https://saltire.lti.app/tool, published test key), site type with name/email sending off.
Learning mode: Join opens the room in a new tab via `instance::get_join_url()`; recordings-only rooms can't be joined;
"wait for moderator" shows "Waiting for the host" until the meeting runs (one server call, learners only). Staff get a
"Recordings and attendance" link to Moodle's page (restyled: room card, recordings table). LTI embeds in the stage unless
the tool's launch container is New window / Same window, which gets a launch card instead (tools that refuse frames).
Names: Siraj classes now pass `'escape' => false` to `format_string` / `get_formatted_name` because Mustache escapes
`{{name}}` itself (was showing `Q&amp;A`). Never print those values with `{{{ }}}`.

## Siraj AI (20 Nov 2026)
Siraj draws the panel; the Capybots assistant answers (local_capybots >= 0.11.0 + the Capybots hub, bot "Siraj staging").
- `local_siraj\ai::config()` uses `\local_capybots\widget::pass()` (null = no AI on this page: not connected, role off,
  quiz attempt/review, guest). `ai::page()` adds course, cmid, activity and UI language.
- `theme_siraj/templates/ai.mustache`: one panel for every page (top-bar sparkle button, Home card, course card, player
  button and chips). Anything with `data-siraj-ai="prompt"` opens it (empty = just open; `data-siraj-ai-mode="quiz"`
  = practice questions). The browser posts to `{hub}/api/public/bot/{bot}/message` (SSE stream; JSON when live
  lookups ran) and `/quiz`, with the visible lesson text (6000 chars) as `page.text`. Replies: escaped, small Markdown.
  Conversation kept per tab (sessionStorage `siraj-ai`).
- Capybots' own bubble is off: `set_config('bubble', 0, 'local_capybots')` after installing.
- Not connected = the old "Coming soon" cards. Connect at /local/capybots/setup.php with a connection code from
  Capybots admin (Clients > Siraj staging > bot > LMS plugin > Create connection code). The bot knows nothing until the
  plugin's course sync has run. Live lookups need a site the hub can reach (staging, not local docker).
Tested live on staging 20 Nov 2026 (EN + AR): lesson explain grounded on page.text, Quiz me, live "what's due" lookups.
Quiz me topic: the activity name, or the course name on quiz/assignment pages.

## Moodle teacher/admin pages (20 Nov 2026)
`theme_siraj/scss/siraj/moodlepages.scss` (last in the SCSS parts list): every Moodle page gets a left-aligned header,
a quiet breadcrumb, toolbars without slabs, alerts as cards with a coloured edge, small A-Z pills, names in tables
in normal case, readable highlights and restriction notes, dark-safe imported descriptions, SCORM teacher view card,
course Reports as cards. Moodle multiplies some badges (mix-blend-mode) which turns them black on dark: reset there.
Sidebar edit button: greyed out (not hidden) for staff on pages with nothing to edit. /course/index.php and course
search go to Siraj Explore for everyone except in edit mode. Home banner: slides clipped to the rounded corners.
