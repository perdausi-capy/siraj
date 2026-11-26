<?php
// Cyber Security Basics: the flagship demo course (Digital & Security). Uses every Siraj block and most activity types.
return [
    'shortname' => 'CYB101',
    'fullname' => 'Cyber Security Basics',
    'category' => 'digital',
    'mandatory' => true,
    'minutes' => 45,
    'cover' => 'img/photo/infosec.jpg',
    'summary' => 'Most cyber attacks start with a person, not a computer. In 45 minutes you will learn the habits that stop them: spotting phishing, signing in safely, handling data with care and reporting fast.',
    'outcomes' => [
        'Spot the warning signs of a phishing message',
        'Protect your accounts with passphrases and multi-factor sign-in',
        'Store, share and dispose of company data safely',
        'Report a security incident within the first hour',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why security is everyone’s job, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Cyber Security Basics', 'You are the first line of defence',
                        'Attackers rarely break in. They are let in: by a click, a reused password or a USB stick from the car park. A few small habits shut most of those doors.') .
                    sb_stats([
                        ['9 in 10', 'cyber incidents start with a human action, such as a click or a shared password'],
                        ['1 hour', 'is how quickly you should report something that looks wrong'],
                        ['45 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'How to recognise a phishing message before you click',
                        'How to create passwords that are easy to remember and hard to guess',
                        'How to handle company data on any device',
                        'Exactly what to do, and who to tell, when something goes wrong',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons, each with real examples from everyday work.'],
                        ['Practise', 'Try a quick activity in each lesson, share what you have seen and submit one short report.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought and download your certificate.'],
                    ]) .
                    sb_note('tip', 'Take it with you', sb_p('There is a one-page quick reference card in Lesson 3. Keep it by your desk.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: which one is risky?', 'package' => sb_h5p('multiple-choice-2-6', 'Which one is risky?', [
                    'question' => '<p>Three emails arrive on a Monday morning. Which one should make you stop and check before doing anything?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>“Your mailbox is full. Sign in within 2 hours to keep your emails.”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. Urgency and a request to sign in are two classic phishing signs.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>A calendar invite from your manager for this week’s team meeting.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>An expected invite from someone you know is low risk.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>The monthly company newsletter you receive every first Monday.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>A regular, expected newsletter is low risk.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Spot the phish',
            'summary' => 'What phishing looks like, the five warning signs, and what to do when you see one.',
            'items' => [
                ['type' => 'book', 'name' => 'Spotting phishing', 'chapters' => [
                    ['What phishing is', sb_banner('Lesson 1', 'Act now, think later', 'That is what every phishing message wants from you.') .
                        sb_p('Phishing is a message that pretends to come from someone you trust, a bank, a supplier, IT or even your own manager, to get you to click a link, open a file, share a password or move money.',
                            'It arrives by email most often, but also by text message (smishing), phone call (vishing) and chat apps. The tools change. The trick is always the same: <strong>create pressure, then ask for something</strong>.') .
                        sb_quote('Attackers don’t hack in. They log in.', 'A common saying among security teams')],
                    ['Five warning signs', sb_p('Most phishing messages show at least one of these signs. Two or more? Stop and check.') .
                        sb_steps([
                            ['Urgency or a threat', '“Your account will be closed today.” Pressure is designed to stop you thinking.'],
                            ['A sender that is almost right', 'support@micros0ft-help.com, or your CEO writing from a personal address.'],
                            ['Links that don’t match', 'Hover before you click. If the real address differs from the text, don’t open it.'],
                            ['Unexpected attachments', 'An invoice you didn’t expect, a “voicemail” as a file, a shared document you never asked for.'],
                            ['A request for something valuable', 'Passwords, sign-in codes, gift cards or a change to bank details.'],
                        ]) .
                        sb_note('warn', 'Watch out', sb_p('Phishing messages are now often well written and use real names from your company, found online. Good grammar is no longer a sign of safety.'))],
                    ['What to do', sb_dodont(
                            ['Pause, and read it again slowly', 'Check with the sender using a number or address you already know', 'Use the Report button in your email', 'Delete it after reporting'],
                            ['Click links or open attachments to “see what it is”', 'Reply, even to say no', 'Forward it to colleagues as a warning', 'Feel embarrassed to report, even if you clicked']) .
                        sb_scenario('An email from “Finance” says a supplier has new bank details and asks you to update them before today’s payment run. The email address looks right and the logo is correct.',
                            'What would you do? (Answer: call the supplier on the number you already have, not one in the email, before changing anything.)')],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: complete the checklist', 'package' => sb_h5p('filltheblanks', 'Complete the checklist', [
                    'text' => '<p>Fill in the missing words. Each word is one of the warning signs from this lesson.</p>',
                    'questions' => ['<p>“Act now or your account will be closed” is a sign of *urgency*.</p><p>Before clicking, *hover* over a link to see where it really goes.</p><p>A request to change bank details should be checked by *phone* on a number you already know.</p><p>If something looks wrong, use the *Report* button.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 2 · Passwords and sign-in',
            'summary' => 'Passphrases, password managers and multi-factor sign-in.',
            'items' => [
                ['type' => 'page', 'name' => 'Signing in safely', 'content' =>
                    sb_banner('Lesson 2', 'Make a stolen password useless', 'Long passphrases and multi-factor sign-in keep attackers out, even when a password leaks.') .
                    sb_columns([
                        ['Passphrases beat passwords', sb_p('Four or more random words, like <em>lantern-river-cobalt-tuesday</em>, are long, easy to remember and very hard to guess.')],
                        ['One account, one password', sb_p('When a website you use is breached, attackers try the same password everywhere else. A unique password per account stops that.')],
                    ]) .
                    '<h3>Set up multi-factor sign-in</h3>' .
                    sb_steps([
                        ['Install the authenticator app', 'Use the app your IT team recommends on your work phone.'],
                        ['Scan the code', 'Open your account security page and scan the QR code with the app.'],
                        ['Keep your backup codes', 'Store them in the password manager, not in a note on your desk.'],
                    ]) .
                    sb_note('warn', 'Never approve a prompt you didn’t start', sb_p('If your phone asks you to approve a sign-in you didn’t make, someone has your password. Press <strong>Deny</strong> and report it straight away.')) .
                    '<h3>Questions people ask</h3>' .
                    sb_faq([
                        ['Can I write my password down?', 'Not on paper or in a document. Use the company password manager, which keeps it encrypted.'],
                        ['How often should I change my password?', 'Only when there is a reason, such as a breach or a suspicious sign-in. Long, unique passwords don’t need regular changes.'],
                        ['IT asked me for my sign-in code. Should I give it?', 'No. Real IT staff never ask for your password or sign-in codes.'],
                    ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Handle data safely',
            'summary' => 'Storing, sharing and disposing of company information, at the office and on the move.',
            'items' => [
                ['type' => 'page', 'name' => 'Data on every device', 'content' =>
                    sb_banner('Lesson 3', 'Keep data where it belongs', 'Treat company information like cash: in the right place, shared with the right people.') .
                    sb_keypoints('Three rules for company data', [
                        'Store files only in approved company systems, never on personal drives or email',
                        'Share with the smallest group that needs it, and remove access when the work is done',
                        'Lock your screen every time you step away, even for a minute',
                    ]) .
                    sb_dodont(
                        ['Use the company VPN on public Wi-Fi', 'Use privacy screens when travelling', 'Shred printed documents with personal data'],
                        ['Plug in USB sticks you found or were given', 'Send work files to your personal email', 'Talk about confidential work in public places']) .
                    sb_note('info', 'Lost a laptop or phone?', sb_p('Report it the same hour. IT can lock and wipe a device remotely, but only if they know quickly.'))],
                ['type' => 'glossary', 'name' => 'Security terms', 'intro' => 'Plain-language definitions of the words used in this course.', 'entries' => [
                    ['Phishing', '<p>A message pretending to be from someone you trust, designed to make you click, share or pay.</p>'],
                    ['Multi-factor sign-in (MFA)', '<p>Signing in with something you know (a password) and something you have (your phone or a key).</p>'],
                    ['Passphrase', '<p>A password made of several random words. Long, memorable and hard to guess.</p>'],
                    ['Malware', '<p>Software designed to damage a device or steal from it, often hidden in attachments or downloads.</p>'],
                    ['Ransomware', '<p>Malware that locks your files and demands payment to unlock them.</p>'],
                    ['Social engineering', '<p>Manipulating people, rather than computers, to get access or information.</p>'],
                ]],
                ['type' => 'file', 'name' => 'Quick reference card (PDF)', 'file' => 'files/cyber-quick-card.pdf',
                    'intro' => 'One page with the warning signs, sign-in rules and what to do if something looks wrong.'],
                ['type' => 'url', 'name' => 'Further reading: top tips for staff', 'url' => 'https://www.ncsc.gov.uk/collection/top-tips-for-staff',
                    'intro' => 'Free e-learning and tips from the UK National Cyber Security Centre.'],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share what you have seen, and practise reporting a suspicious message.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: which risk do you see most?', 'intro' => 'Pick the one you come across most often at work. Results are shown after you answer.',
                    'options' => ['Phishing emails', 'Suspicious phone calls', 'Fake text messages', 'People asking for access they shouldn’t have']],
                ['type' => 'forum', 'name' => 'Share a phishing attempt you have seen', 'intro' => 'Describe a suspicious message you received and what gave it away. Never include passwords, links or personal data.',
                    'posts' => [['Welcome: what gave it away?', 'Tell us about a phishing attempt you spotted, and the sign that made you stop. Your example could help a colleague spot the next one.']]],
                ['type' => 'assign', 'name' => 'Report a suspicious message', 'dueindays' => 21, 'words' => 200,
                    'intro' => 'Practise writing the short report the security desk needs.',
                    'instructions' => sb_p('Imagine you received the supplier email from Lesson 1. Write the report you would send to the IT security desk, in 200 words or fewer.') .
                        sb_keypoints('Include', ['When you received it and from which address', 'What it asked you to do', 'Which warning signs you noticed', 'Whether you clicked, replied or opened anything'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Spotting phishing', 'Signing in safely', 'Data on every device'], 'questions' => [
                    ['mc', 'An email says your mailbox is full and asks you to sign in within two hours. What should you do?',
                        'Report it with the Report button and delete it', ['Sign in quickly to keep your emails', 'Reply to ask if it is real', 'Forward it to your team as a warning'],
                        'Urgency plus a request to sign in are classic phishing signs. Reporting lets the security team block it for everyone.'],
                    ['mc', 'Which of these is the strongest password?',
                        'lantern-river-cobalt-tuesday', ['Summer2026!', 'Your name and year of birth', 'The same long password on every site'],
                        'A passphrase of random words is long and hard to guess, and should be used for one account only.'],
                    ['tf', 'Real IT staff may ask for your sign-in code when they are fixing your account.', false,
                        'Never share a sign-in code. Real IT staff never ask for passwords or codes.'],
                    ['mc', 'Your phone asks you to approve a sign-in that you didn’t start. What does this mean?',
                        'Someone has your password: deny it and report it', ['It is a routine check: approve it', 'Your phone needs an update', 'Nothing: ignore it'],
                        'An unexpected prompt means someone is trying to sign in as you. Deny it and report it straight away.'],
                    ['tf', 'It is fine to store work files on your personal cloud drive if it has a strong password.', false,
                        'Company data belongs only in approved company systems, where it is backed up and protected.'],
                    ['mc', 'You clicked a link and then realised the email was fake. What should you do?',
                        'Report it to the IT security desk within the hour', ['Wait and see if anything happens', 'Delete the email and say nothing', 'Restart your computer and carry on'],
                        'Reporting quickly lets IT contain the damage. Nobody is blamed for reporting an honest mistake.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Stop, check, report', 'Three habits that stop most attacks.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Stop</strong> when a message creates pressure or asks for something valuable.',
                        '<strong>Check</strong> with the sender using details you already have.',
                        '<strong>Report</strong> anything suspicious, and report your own mistakes within the hour.',
                    ])) .
                    sb_quote('Security is not a product, but a process.', 'Bruce Schneier, security technologist') .
                    sb_p('Thank you for completing Cyber Security Basics. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about spotting phishing?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
