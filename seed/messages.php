<?php
// Demo chat: a few messages between layla and colleagues, so the message drawer has real conversations. Dummy users only.
// Safe to run again (skips when layla already has a conversation with that person).
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
$layla = $DB->get_record('user', ['username' => 'layla'], '*', MUST_EXIST);
$threads = [
    'omar' => [['omar', 'Hi Layla, quick reminder that the Workplace Health & Safety knowledge check closes on Friday.'],
        ['layla', 'Thanks Omar! I have one activity left, I will finish it today.'],
        ['omar', 'Great. Let me know if the quiz gives you any trouble.']],
    'sara' => [['sara', 'Did you see the new Leadership course in Explore?'],
        ['layla', 'Yes, it looks good. Are you taking it too?'],
        ['sara', 'Starting next week. Want to go through it together?']],
];
$now = time();
foreach ($threads as $other => $msgs) {
    if (!$u = $DB->get_record('user', ['username' => $other])) {
        echo "$other: not found\n";
        continue;
    }
    if (\core_message\api::get_conversation_between_users([$layla->id, $u->id])) {
        echo "$other: already has a conversation\n";
        continue;
    }
    $conv = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, [$layla->id, $u->id]);
    foreach ($msgs as $i => [$from, $text]) {
        $sender = $from === 'layla' ? $layla : $u;
        $id = $DB->insert_record('messages', ['useridfrom' => $sender->id, 'conversationid' => $conv->id, 'subject' => null,
            'fullmessage' => $text, 'fullmessageformat' => FORMAT_PLAIN, 'fullmessagehtml' => '', 'smallmessage' => $text,
            'timecreated' => $now - (count($msgs) - $i) * 900 - ($other === 'sara' ? 86400 : 0)]);
    }
    echo "$other: " . count($msgs) . " messages\n";
}
