<?php
// Real feedback for the demo quiz questions. They were made with Moodle's test generator, which fills in test text
// ("Frog is a very good answer", "The oddest number is One", a hidden "toad" answer worth 80%). This sets a short
// explanation per question, plain right/wrong feedback on the answers, and removes the test answers.
// Run: php siraj_seed/quizfeedback.php (also run at the end of democontent.php). Safe to run again.
defined('CLI_SCRIPT') || define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../config.php');

// Question text (start) => explanation shown after answering.
$explain = [
    'What is the first step of a risk assessment?' => 'You can only control a risk once you know it is there, so every risk assessment starts by identifying the hazards.',
    'You should report a near miss even if nobody was hurt.' => 'A near miss shows where the next accident could happen. Reporting it lets the team fix the cause before anyone is hurt.',
    'Within how many hours should an incident be reported?' => 'Report incidents within 24 hours, while details are fresh and before the cause can repeat.',
    'What should you do first if you have a conflict of interest?' => 'Declaring it in writing to your manager keeps the decision transparent and protects you and the company.',
    'Speaking up in good faith is protected' => 'You are protected when you raise a concern honestly, even if it turns out there was no problem.',
    'Name one of the four values of the Code' => 'The four values of the Code are integrity, respect, accountability and care. Any one of them is right.',
    'Which is the strongest password?' => 'A long passphrase used only for one account is hard to guess and cannot be reused against you if another site is breached.',
    'Multi-factor sign-in protects your account' => 'With multi-factor sign-in, a stolen password alone is not enough: the attacker also needs your second factor.',
    'How quickly should you report a suspected security incident' => 'Report a suspected security incident within one hour. The sooner it is reported, the less damage it can do.',
    'Which extinguisher should you use on burning electrical equipment?' => 'CO2 puts out electrical fires without conducting electricity. Water and wet chemical extinguishers can cause electric shock.',
    'You should use the lift during an evacuation' => 'Never use a lift during an evacuation: it can stop or take you to the fire floor. Use the stairs.',
    'Where do you go after leaving the building?' => 'Go straight to the assembly point so the fire warden can check that everyone is out.',
    'If a stakeholder adds scope, what usually has to change?' => 'More work needs more time or more money. Agree the change to the schedule or the budget before accepting new scope.',
    'The critical path is the longest chain' => 'The critical path sets the shortest possible project length: any delay on it delays the whole project.',
    'How often should you send a project update?' => 'A short weekly update keeps stakeholders informed without overloading them.',
    'In the SBI feedback model, what does the B stand for?' => 'SBI stands for Situation, Behaviour, Impact: describe what the person did, not their character.',
    'A one-to-one is mainly for the manager to give updates.' => 'A one-to-one is the team member\'s time: their priorities, blockers and development come first.',
    'How many minutes is a typical weekly one-to-one' => 'This course recommends 30 minutes a week: long enough for a real conversation, short enough to keep every week.',
    'ما الخطوة الأولى في تقييم المخاطر؟' => 'لا يمكن التحكم في خطر قبل معرفته، لذلك يبدأ كل تقييم للمخاطر بتحديدها.',
    'ما أول خطوة عند تلقي شكوى؟' => 'الاستماع حتى النهاية يُشعر العميل بالاهتمام ويوضح المشكلة الحقيقية قبل اقتراح أي حل.',
];
$arabic = fn(string $text) => (bool)preg_match('/\p{Arabic}/u', $text);
$words = [
    false => ['right' => 'Correct.', 'wrong' => 'Not quite.', 'partial' => 'Partly right.'],
    true => ['right' => 'إجابة صحيحة.', 'wrong' => 'ليست الإجابة الصحيحة.', 'partial' => 'إجابة صحيحة جزئيًا.'],
];

$fixed = 0;
foreach ($DB->get_records_select('question', "qtype IN ('multichoice', 'truefalse', 'shortanswer')") as $q) {
    $text = trim(html_entity_decode(strip_tags($q->questiontext)));
    $note = null;
    foreach ($explain as $start => $why) {
        if (strpos($text, $start) === 0) {
            $note = $why;
            break;
        }
    }
    if ($note === null) {
        continue; // Not a demo question: leave it alone.
    }
    $w = $words[$arabic($text)];
    $DB->update_record('question', (object)['id' => $q->id, 'generalfeedback' => '<p>' . s($note) . '</p>',
        'generalfeedbackformat' => FORMAT_HTML]);
    if ($q->qtype === 'shortanswer') {
        // The generator's extra answers ("toad", 80%) go; the real answer and the catch-all stay.
        $DB->delete_records_select('question_answers', "question = ? AND fraction > 0 AND fraction < 1", [$q->id]);
        // "Name one of the four values": each of the four is a full-mark answer.
        if (strpos($text, 'Name one of the four values') === 0) {
            foreach (['respect', 'accountability', 'care'] as $value) {
                if (!$DB->record_exists_select('question_answers', 'question = ? AND ' . $DB->sql_compare_text('answer') . ' = ?', [$q->id, $value])) {
                    $DB->insert_record('question_answers', (object)['question' => $q->id, 'answer' => $value, 'answerformat' => FORMAT_MOODLE,
                        'fraction' => 1, 'feedback' => '', 'feedbackformat' => FORMAT_HTML]);
                }
            }
        }
        // Answers are checked in order: the catch-all ("*", any other answer) must come last.
        $all = $DB->get_records('question_answers', ['question' => $q->id], 'id');
        $catchall = array_filter($all, fn($a) => trim($a->answer) === '*');
        if ($catchall && array_key_last($all) !== array_key_first($catchall)) {
            $c = reset($catchall);
            $DB->delete_records('question_answers', ['id' => $c->id]);
            unset($c->id);
            $DB->insert_record('question_answers', $c);
        }
    }
    foreach ($DB->get_records('question_answers', ['question' => $q->id]) as $a) {
        $feedback = $q->qtype === 'multichoice' ? '' : ($a->fraction >= 1 ? $w['right'] : $w['wrong']);
        $DB->update_record('question_answers', (object)['id' => $a->id, 'feedback' => $feedback ? "<p>$feedback</p>" : '',
            'feedbackformat' => FORMAT_HTML]);
    }
    if ($q->qtype === 'multichoice') {
        $DB->set_field('qtype_multichoice_options', 'correctfeedback', "<p>{$w['right']}</p>", ['questionid' => $q->id]);
        $DB->set_field('qtype_multichoice_options', 'partiallycorrectfeedback', "<p>{$w['partial']}</p>", ['questionid' => $q->id]);
        $DB->set_field('qtype_multichoice_options', 'incorrectfeedback', "<p>{$w['wrong']}</p>", ['questionid' => $q->id]);
    }
    $fixed++;
}
// Questions are cached; clear them so attempts and reviews show the new text.
\cache::make('core', 'questiondata')->purge();
echo "quiz feedback: $fixed questions\n";
