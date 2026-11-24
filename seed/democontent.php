<?php
// Local lab: demo-ready course content. Real lesson text, outcomes and quiz questions for the 8 demo courses,
// covers for the courses without one, and two courses written in Arabic. Content is written for demos; it is not
// legal or compliance advice. Local only.
// Run: php siraj_seed/democontent.php   (after seed.php, fields.php, team.php, realistic.php)
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();
$fs = get_file_storage();

$p = fn(...$lines) => implode('', array_map(fn($l) => str_starts_with($l, '<') ? $l : "<p>$l</p>", $lines));
$ul = fn(...$items) => '<ul>' . implode('', array_map(fn($i) => "<li>$i</li>", $items)) . '</ul>';
$out = fn(...$items) => '<p><b>By the end you will be able to:</b></p>' . $ul(...$items);

// shortname => [summary, outcomes[], welcome page, second page, quiz: [mc question, correct, 3 wrong], [true/false statement, true?], [short question, answer]]
$courses = [
    'HSE101' => [
        'Site rules, risk assessment and reporting near misses. Mandatory for all staff every year.',
        ['Spot the five most common hazards in your workplace', 'Carry out a simple five-step risk assessment', 'Report an incident or near miss the same day'],
        $p('Most workplace injuries come from a handful of everyday hazards: slips and trips, manual handling, working at height, moving vehicles and electricity. This course shows you how to spot them before they hurt someone.',
           'You will learn the five-step risk assessment used across the company, and exactly what to do when something goes wrong, or nearly does.'),
        $p('<b>The five steps of a risk assessment</b>') . '<ol><li>Identify the hazards.</li><li>Decide who might be harmed and how.</li><li>Evaluate the risk and decide on controls.</li><li>Record what you found and what you did.</li><li>Review it when anything changes, and at least once a year.</li></ol>'
           . $p('A <b>near miss</b> is an event that could have caused harm but did not: a box that fell where someone had been standing a second earlier. Report near misses as seriously as injuries. They are free lessons.'),
        ['What is the first step of a risk assessment?', 'Identify the hazards', 'Buy new safety equipment', 'Write the incident report', 'Ask HR for approval'],
        ['You should report a near miss even if nobody was hurt.', true],
        ['Within how many hours should an incident be reported? (number only)', '24'],
    ],
    'COC101' => [
        'How we work with clients, partners and each other. Includes anti-bribery and conflicts of interest.',
        ['Explain the four values behind the Code of Conduct', 'Recognise a conflict of interest and declare it', 'Know where to raise a concern safely'],
        $p('Our Code of Conduct describes how we treat clients, partners and each other. It rests on four values: integrity, respect, accountability and care.',
           'Nobody can write a rule for every situation. When you are unsure, ask: would I be comfortable if this appeared in tomorrow\'s news, and could I explain it to my manager?'),
        $p('<b>Conflicts of interest</b>', 'A conflict of interest exists when your personal interests could influence, or look like they influence, a decision you make at work: hiring a relative, or choosing a supplier owned by a friend.', 'Having a conflict is not wrong. Hiding it is. Declare it in writing to your manager before the decision is made.')
           . $p('<b>Speaking up</b>', 'You can raise a concern with your manager, HR or the confidential ethics line. Retaliation against anyone who speaks up in good faith is a breach of this Code.'),
        ['What should you do first if you have a conflict of interest?', 'Declare it in writing to your manager', 'Keep it private', 'Resign from the project', 'Ask a colleague to decide for you'],
        ['Speaking up in good faith is protected, even if the concern turns out to be unfounded.', true],
        ['Name one of the four values of the Code (one word).', 'integrity'],
    ],
    'SEC101' => [
        'Phishing, passwords, data handling and what to do if something looks wrong.',
        ['Spot the signs of a phishing email', 'Create strong passphrases and use multi-factor sign-in', 'Report a security incident in the first hour'],
        $p('Most cyber attacks start with a person, not a computer: a convincing email, a phone call that sounds urgent, a USB stick left in the car park. You are the first line of defence.',
           'This course covers the habits that stop most attacks: spotting phishing, strong passwords with multi-factor sign-in, handling data carefully, and reporting quickly.'),
        $p('<b>Five signs of a phishing email</b>') . $ul('Urgent pressure: "your account will be closed today".', 'A sender address that is almost right.', 'Links whose real address differs from the text.', 'Unexpected attachments or invoices.', 'Requests for passwords, codes or payment changes.')
           . $p('If something looks wrong, do not click. Use the <b>Report phishing</b> button, or forward it to the IT security desk, and delete it.'),
        ['Which is the strongest password?', 'A four-word passphrase you do not use anywhere else', 'Your name and year of birth', 'Password123!', 'The same strong password on every site'],
        ['Multi-factor sign-in protects your account even if your password is stolen.', true],
        ['How quickly should you report a suspected security incident, in hours? (number only)', '1'],
    ],
    'FIRE101' => [
        'Evacuation routes, extinguisher types and the role of fire wardens.',
        ['Follow the evacuation procedure for your building', 'Choose the right extinguisher for a fire', 'Know what fire wardens do and how to support them'],
        $p('Fire spreads fast: a small fire can fill a room with smoke in under three minutes. Knowing what to do before the alarm sounds is what keeps people safe.',
           'This course covers raising the alarm, evacuating calmly, extinguisher types, and the role of your floor\'s fire wardens.'),
        $p('<b>When the alarm sounds</b>') . '<ol><li>Stop work and leave by the nearest safe exit. Do not use lifts.</li><li>Close doors behind you to slow the fire.</li><li>Go to your assembly point and report to the fire warden.</li><li>Do not go back in until the fire service says it is safe.</li></ol>'
           . $p('<b>Extinguishers</b>: water for paper and wood, CO2 for electrical equipment, foam for flammable liquids, wet chemical for cooking oil. Only tackle a fire if it is small, you are trained, and you have a clear way out.'),
        ['Which extinguisher should you use on burning electrical equipment?', 'CO2', 'Water', 'Wet chemical', 'Any extinguisher will do'],
        ['You should use the lift during an evacuation if you are on a high floor.', false],
        ['Where do you go after leaving the building? (two words)', 'assembly point'],
    ],
    'PM201' => [
        'Scope, schedule, cost and stakeholders: the basics every project team member needs.',
        ['Write a clear project scope with what is in and out', 'Build a simple schedule and find the critical path', 'Keep stakeholders informed with a weekly update'],
        $p('Most projects that fail do not fail because of hard technical problems. They fail because the goal was unclear, the plan was optimistic, or nobody said early enough that things were slipping.',
           'This course gives you the basics used on every project in the company: scope, schedule, stakeholders and simple weekly reporting.'),
        $p('<b>The project triangle</b>', 'Scope, time and cost are linked. Change one, and at least one of the others moves. When a stakeholder asks for more scope, the honest answer is: "Yes, and here is what it does to the date or the budget."')
           . $p('<b>The weekly update</b>', 'Three lines are enough: what we finished, what we will do next, and what is at risk. Red is not a failure; red that is reported early is a project working well.'),
        ['If a stakeholder adds scope, what usually has to change?', 'The schedule or the budget', 'Nothing, if the team works harder', 'Only the project name', 'The team members'],
        ['The critical path is the longest chain of dependent tasks in a project.', true],
        ['How often should you send a project update? (one word)', 'weekly'],
    ],
    'LEAD301' => [
        'Coaching, feedback and running effective one-to-ones. Open for self-enrolment.',
        ['Run a 30-minute one-to-one that your team values', 'Give feedback with the situation-behaviour-impact model', 'Coach instead of solving problems for people'],
        $p('Moving from doing the work to leading the people who do it is one of the hardest steps in a career. This course focuses on the three habits that matter most for new line managers.',
           'You will practise one-to-ones, feedback and coaching questions you can use the same week.'),
        $p('<b>Situation, behaviour, impact</b>', 'Describe the situation ("In Tuesday\'s client meeting"), the behaviour you saw ("you summarised the decisions at the end"), and its impact ("the client said it was the clearest meeting they had had"). It works for praise and for correction.'),
        ['In the SBI feedback model, what does the B stand for?', 'Behaviour', 'Budget', 'Benefit', 'Background'],
        ['A one-to-one is mainly for the manager to give updates.', false],
        ['How many minutes is a typical weekly one-to-one in this course? (number only)', '30'],
    ],
    'ABC101' => [
        'Gifts, hospitality and what to do if someone offers you something.',
        ['Tell a reasonable gift from a bribe', 'Use the gifts and hospitality register', 'Respond safely if you are offered a bribe'],
        $p('A bribe is anything of value offered to influence a decision improperly. It does not have to be cash: a trip, a job for a relative or an expensive dinner can all be bribes.',
           'Bribery laws in the UAE, the UK and the US can apply to our company wherever we work, and to you personally.'),
        $p('<b>Before you accept any gift or hospitality, ask:</b>') . $ul('Is it modest, occasional and openly given?', 'Is the giver waiting for a decision from me?', 'Would I be comfortable recording it in the register?')
           . $p('Record every gift or hospitality over the company limit in the register within five days. If you are offered a bribe, refuse it clearly, and report it the same day.'),
        ['What should you do if a supplier offers you cash to win a contract?', 'Refuse clearly and report it the same day', 'Accept it and give it to charity', 'Ignore it', 'Ask for a larger amount in writing'],
        ['A bribe can be something other than money, such as a job for a relative.', true],
        ['Within how many days must gifts over the limit be recorded? (number only)', '5'],
    ],
    'DPE101' => [
        'Personal data, consent and the UAE PDPL in plain language.',
        ['Recognise personal and sensitive data', 'Handle personal data on a need-to-know basis', 'Know what to do when data goes to the wrong place'],
        $p('Personal data is any information that identifies a person: a name, an Emirates ID number, an email address, a photo. Sensitive data, such as health or financial information, needs extra care.',
           'This course explains the main ideas of data protection in plain language, and the everyday habits that keep our clients\' and colleagues\' data safe.'),
        $p('<b>Everyday rules</b>') . $ul('Only collect what you need for the task.', 'Share on a need-to-know basis, through approved systems.', 'Lock your screen, and never send personal data to a personal email.', 'Keep it only as long as needed, then delete it securely.')
           . $p('If personal data goes to the wrong person or place, report it to the data protection officer immediately. Speed matters.'),
        ['Which of these is personal data?', 'An employee\'s email address', 'The company\'s office address', 'A public press release', 'The weather forecast'],
        ['You may send client personal data to your personal email if you need to work from home.', false],
        ['Who should you tell first if data goes to the wrong place? (abbreviation)', 'DPO'],
    ],
];

foreach ($courses as $short => [$summary, $outcomes, $welcome, $second, $mc, $tf, $sa]) {
    $course = $DB->get_record('course', ['shortname' => $short]);
    if (!$course) {
        continue;
    }
    $DB->update_record('course', (object)['id' => $course->id, 'summary' => "<p>$summary</p>" . $out(...$outcomes),
        'summaryformat' => FORMAT_HTML]);
    $modinfo = get_fast_modinfo($course);
    $pages = array_values(array_filter($modinfo->get_instances_of('page'), fn($cm) => $cm->name !== 'About this course'));
    foreach ([$welcome, $second] as $i => $content) {
        if (isset($pages[$i])) {
            $page = $DB->get_record('page', ['id' => $pages[$i]->instance]);
            $DB->update_record('page', (object)['id' => $page->id, 'content' => $content, 'contentformat' => FORMAT_HTML,
                'revision' => $page->revision + 1, 'intro' => '', 'timemodified' => time()]);
        }
    }
    // Quiz questions: the three questions each demo quiz has (multiple choice, true/false, short answer).
    foreach ($modinfo->get_instances_of('quiz') as $cm) {
        $slots = $DB->get_records_sql("SELECT q.id, q.qtype FROM {quiz_slots} s
                                         JOIN {question_references} r ON r.itemid = s.id AND r.component = 'mod_quiz' AND r.questionarea = 'slot'
                                         JOIN {question_versions} v ON v.questionbankentryid = r.questionbankentryid
                                         JOIN {question} q ON q.id = v.questionid
                                        WHERE s.quizid = ? ORDER BY s.slot", [$cm->instance]);
        foreach ($slots as $q) {
            $answers = array_values($DB->get_records('question_answers', ['question' => $q->id], 'id'));
            if ($q->qtype === 'multichoice') {
                $DB->update_record('question', (object)['id' => $q->id, 'questiontext' => "<p>{$mc[0]}</p>", 'name' => 'Q1']);
                // Correct answer first (the one with full marks), then the three wrong ones.
                usort($answers, fn($a, $b) => $b->fraction <=> $a->fraction);
                foreach (array_slice($mc, 1) as $k => $text) {
                    if (isset($answers[$k])) {
                        $DB->update_record('question_answers', (object)['id' => $answers[$k]->id, 'answer' => "<p>$text</p>",
                            'answerformat' => FORMAT_HTML, 'feedback' => '']);
                    }
                }
            } else if ($q->qtype === 'truefalse') {
                $DB->update_record('question', (object)['id' => $q->id, 'questiontext' => "<p>{$tf[0]}</p>", 'name' => 'Q2']);
                $tfrow = $DB->get_record('question_truefalse', ['question' => $q->id]);
                if ($tfrow) {
                    $DB->set_field('question_answers', 'fraction', $tf[1] ? 1 : 0, ['id' => $tfrow->trueanswer]);
                    $DB->set_field('question_answers', 'fraction', $tf[1] ? 0 : 1, ['id' => $tfrow->falseanswer]);
                }
            } else if ($q->qtype === 'shortanswer') {
                $DB->update_record('question', (object)['id' => $q->id, 'questiontext' => "<p>{$sa[0]}</p>", 'name' => 'Q3']);
                usort($answers, fn($a, $b) => $b->fraction <=> $a->fraction);
                if (isset($answers[0])) {
                    $DB->update_record('question_answers', (object)['id' => $answers[0]->id, 'answer' => $sa[1], 'fraction' => 1]);
                }
            }
        }
    }
    rebuild_course_cache($course->id, true);
    echo "content: $short\n";
}

// Covers for the courses created later.
foreach (['ABC101' => 'abc', 'DPE101' => 'dpe'] as $short => $img) {
    $course = $DB->get_record('course', ['shortname' => $short]);
    $ctx = context_course::instance($course->id);
    if (!$fs->get_area_files($ctx->id, 'course', 'overviewfiles', 0, 'id', false)) {
        $fs->create_file_from_pathname(['contextid' => $ctx->id, 'component' => 'course', 'filearea' => 'overviewfiles',
            'itemid' => 0, 'filepath' => '/', 'filename' => "$img.png"], __DIR__ . "/img/$img.png");
        echo "cover: $short\n";
    }
}

// Two courses written in Arabic, for Arabic demos.
$cat = $DB->get_record('course_categories', ['name' => 'Compliance'], '*', MUST_EXIST);
$pd = $DB->get_record('course_categories', ['name' => 'Professional Development'], '*', MUST_EXIST);
$arabic = [
    ['AR-HSE', 'أساسيات السلامة في مكان العمل', $cat->id, 'ar-safety', 1, 30,
        'المخاطر الأكثر شيوعًا في العمل، وتقييم المخاطر بخمس خطوات، والإبلاغ عن الحوادث الوشيكة.',
        ['التعرف على المخاطر الخمسة الأكثر شيوعًا', 'إجراء تقييم بسيط للمخاطر', 'الإبلاغ عن أي حادث في اليوم نفسه'],
        [['مرحبًا بك', $p('معظم إصابات العمل تأتي من مخاطر يومية بسيطة: الانزلاق والتعثر، ورفع الأحمال، والعمل على المرتفعات، والمركبات المتحركة، والكهرباء.', 'ستتعلم في هذه الدورة كيف تكتشف هذه المخاطر قبل أن تؤذي أحدًا، وماذا تفعل عندما يحدث خطأ ما.')],
         ['خطوات تقييم المخاطر', '<ol><li>حدد المخاطر.</li><li>حدد من قد يتضرر وكيف.</li><li>قيّم الخطر وقرر الإجراءات.</li><li>سجّل ما وجدته وما فعلته.</li><li>راجع التقييم عند أي تغيير.</li></ol>' . $p('<b>الحادث الوشيك</b> هو حدث كان يمكن أن يسبب ضررًا لكنه لم يفعل. أبلغ عنه بالجدية نفسها التي تبلغ بها عن الإصابة.')]],
        ['ما الخطوة الأولى في تقييم المخاطر؟', 'تحديد المخاطر', 'شراء معدات جديدة', 'كتابة تقرير الحادث', 'طلب موافقة الموارد البشرية']],
    ['AR-CS', 'خدمة العملاء المتميزة', $pd->id, 'ar-service', 0, 45,
        'كيف تستمع للعميل، وتتعامل مع الشكاوى بهدوء، وتحوّل التجربة السيئة إلى ولاء.',
        ['الاستماع الفعّال للعميل', 'التعامل مع الشكاوى بخطوات واضحة', 'متابعة العميل بعد حل المشكلة'],
        [['مرحبًا بك', $p('العميل لا يتذكر دائمًا ما قلته، لكنه يتذكر كيف جعلته يشعر. في هذه الدورة نتعلم مهارات بسيطة تصنع فرقًا كبيرًا في كل تواصل مع العملاء.')],
         ['التعامل مع الشكاوى', '<ol><li>استمع حتى النهاية دون مقاطعة.</li><li>اعترف بالمشكلة واعتذر عن الإزعاج.</li><li>اتفق مع العميل على الحل والموعد.</li><li>تابع للتأكد من رضاه.</li></ol>']],
        ['ما أول خطوة عند تلقي شكوى؟', 'الاستماع حتى النهاية دون مقاطعة', 'شرح سياسة الشركة', 'تحويل العميل لزميل', 'تقديم خصم فورًا']],
];
$handler = \core_course\customfield\course_handler::create();
foreach ($arabic as [$short, $name, $catid, $img, $mandatory, $minutes, $summary, $outcomes, $lessons, $mcq]) {
    if ($DB->record_exists('course', ['shortname' => $short])) {
        continue;
    }
    $course = $gen->create_course(['fullname' => $name, 'shortname' => $short, 'category' => $catid, 'lang' => 'ar',
        'summary' => "<p>$summary</p><p><b>في نهاية الدورة ستتمكن من:</b></p>" . $ul(...$outcomes), 'summaryformat' => FORMAT_HTML,
        'enablecompletion' => 1, 'numsections' => 3, 'format' => 'topics', 'startdate' => time() - 20 * DAYSECS]);
    foreach (['المقدمة', 'المحتوى', 'التقييم'] as $i => $sname) {
        course_update_section($course, $DB->get_record('course_sections', ['course' => $course->id, 'section' => $i + 1]), ['name' => $sname]);
    }
    $ctx = context_course::instance($course->id);
    $fs->create_file_from_pathname(['contextid' => $ctx->id, 'component' => 'course', 'filearea' => 'overviewfiles',
        'itemid' => 0, 'filepath' => '/', 'filename' => "$img.png"], __DIR__ . "/img/$img.png");
    foreach ($lessons as $i => [$title, $content]) {
        $gen->create_module('page', ['course' => $course->id, 'section' => $i + 1, 'name' => $title, 'content' => $content,
            'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]);
    }
    $quiz = $gen->create_module('quiz', ['course' => $course->id, 'section' => 3, 'name' => 'اختبار قصير',
        'intro' => '<p>سؤال واحد للتأكد من فهم النقاط الأساسية.</p>', 'completion' => COMPLETION_TRACKING_AUTOMATIC,
        'completionview' => 1, 'grade' => 10, 'sumgrades' => 1]);
    $qgen = $gen->get_plugin_generator('core_question');
    $qcat = $qgen->create_question_category(['contextid' => context_module::instance($quiz->cmid)->id]);
    $q = $qgen->create_question('multichoice', 'one_of_four', ['category' => $qcat->id, 'name' => 'س1',
        'questiontext' => ['text' => "<p>{$mcq[0]}</p>", 'format' => FORMAT_HTML]]);
    $answers = array_values($DB->get_records('question_answers', ['question' => $q->id], 'fraction DESC, id'));
    foreach (array_slice($mcq, 1) as $k => $text) {
        $DB->update_record('question_answers', (object)['id' => $answers[$k]->id, 'answer' => "<p>$text</p>", 'feedback' => '']);
    }
    quiz_add_quiz_question($q->id, $DB->get_record('quiz', ['id' => $quiz->id]));
    $handler->instance_form_save((object)['id' => $course->id, 'customfield_siraj_mandatory' => $mandatory,
        'customfield_siraj_duration' => (string)$minutes]);
    // Arabic-speaking demo learners take them.
    foreach (['mariam', 'layla', 'noura', 'reem'] as $username) {
        if ($u = $DB->get_record('user', ['username' => $username])) {
            $gen->enrol_user($u->id, $course->id, 'student');
        }
    }
    if ($t = $DB->get_record('user', ['username' => 'hana'])) {
        $gen->enrol_user($t->id, $course->id, 'editingteacher');
    }
    echo "arabic course: $short\n";
}
// Real feedback on the quiz questions (the generator's test text out).
require(__DIR__ . '/quizfeedback.php');
purge_all_caches();
echo "demo content done\n";
