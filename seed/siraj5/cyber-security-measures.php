<?php
// Cyber Security Measures (Digital): built around the existing Articulate Rise lesson (Cyber_Security_Measures).
return [
    'shortname' => 'Cyber Security Measures',
    'fullname' => 'Cyber Security Measures',
    'category' => 'digital',
    'mandatory' => false,
    'minutes' => 30,
    'summary' => 'Practical measures to keep your accounts, data, workplace, computer and social media safe from theft, loss or damage. This is the second part of the cyber security series and helps you build a security mindset.',
    'outcomes' => [
        'Secure your online accounts with strong passwords, multi-factor authentication, updates and encryption',
        'Keep your workstation and physical environment safe',
        'Explain how firewalls, antivirus and encryption protect a computer',
        'Avoid the social media habits that put you and your organisation at risk',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Cyber Security Measures', 'Build a security mindset',
                        'Vulnerable accounts and unattended desks are easy targets. A few steady habits keep your data, your devices and your workplace safe.') .
                    sb_stats([
                        ['4', 'steps to secure an online account'],
                        ['3', 'layers of protection for your computer'],
                        ['30 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'How to secure your online accounts',
                        'Why information security matters to the organisation',
                        'Physical security, environmental controls and keeping your workstation safe',
                        'How firewalls, antivirus and encryption protect your computer',
                        'What to avoid on social media',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through the interactive lesson. It has short knowledge checks along the way.'],
                        ['Review', 'Read the key takeaways page to fix the main ideas.'],
                        ['Reflect', 'Answer a one-question poll about your own habits.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                    ]) .
                    sb_note('tip', 'Part two of a series', sb_p('This course builds on Introduction to Cyber Security. If you haven’t taken that one yet, it is a good place to start.'))],
            ],
        ],
        [
            'name' => 'Lesson',
            'summary' => 'The full interactive lesson, followed by its key takeaways.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Cyber_Security_Measures', 'name' => 'Cyber security measures in practice',
                    'intro' => 'An interactive lesson on account security, information security, physical security, computer security and social media.'],
                ['type' => 'page', 'name' => 'Key takeaways', 'content' =>
                    sb_banner('After the lesson', 'Layer your defences', 'The ideas worth keeping from the lesson, on one page.') .
                    '<h3>Four steps to secure an online account</h3>' .
                    sb_steps([
                        ['Create a strong, unique password', 'Mix upper and lower case letters, numbers and symbols. Avoid personal details, never reuse it and never share it.'],
                        ['Turn on multi-factor authentication', 'A second check, such as a code by text or email, proves it is really you signing in.'],
                        ['Keep software up to date', 'Updates fix bugs and make your devices less vulnerable to attack.'],
                        ['Encrypt sensitive information', 'Encrypted data is unreadable to attackers. Only people with the password or key can open it.'],
                    ]) .
                    sb_columns([
                        ['Physical security', sb_ul(['Fences, barriers, walls, doors, keys and locks', 'Lighting and biometrics', 'Security cameras and alarms'])],
                        ['Environmental controls', sb_ul(['Power supply', 'Humidity level', 'Air conditioning', 'Fire suppression system'])],
                    ]) .
                    sb_dodont(
                        ['Keep your desk clear of sensitive material', 'Shred confidential paper documents', 'Lock or log off when you leave your desk', 'Collect printouts and USB devices straight away'],
                        ['Share your workstation with others', 'Stick passwords on notes where people can see them', 'Leave doors open to secure areas', 'Overshare or click adverts on social media']) .
                    sb_note('info', 'Three layers of protection', sb_p('<strong>Firewalls</strong> filter traffic and block unauthorised access. <strong>Antivirus</strong> protects against malware, spyware, ransomware, worms and viruses. <strong>Encryption</strong> protects the confidential information stored on a computer.')) .
                    sb_note('warn', 'Careful on free Wi-Fi', sb_p('Attackers may see what you do online on a hotspot or free Wi-Fi. Take extra care when signing in to accounts there.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Look at your own habits.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: which habit will you improve first?', 'intro' => 'Pick the measure you most need to work on. Results are shown after you answer.',
                    'options' => ['Using a unique password for every account', 'Turning on multi-factor authentication', 'Keeping my desk clear and locked', 'Sharing less on social media']],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on the lesson.', 'pass' => 7,
                    'after' => ['Cyber security measures in practice', 'Key takeaways'], 'questions' => [
                    ['mc', 'Which of these is the strongest password?',
                        'Kcmmp1204!@#', ['12345678', 'password123', 'A first name followed by a birth year'],
                        'A strong password mixes upper and lower case letters, numbers and symbols, and avoids personal information.'],
                    ['tf', 'Turning on two-factor or multi-factor authentication adds an extra layer of security to your online accounts.', true,
                        'A second check, such as a code sent by text or email, helps prove it is really you signing in.'],
                    ['mc', 'Power supply, humidity level, air conditioning and fire suppression are all part of what?',
                        'Environmental controls', ['Physical access controls', 'Computer security', 'Account security'],
                        'Environmental controls protect the organisation’s assets from damage.'],
                    ['mc', 'What acts as a barrier that filters network traffic and blocks unauthorised access?',
                        'A firewall', ['Antivirus software', 'Encryption', 'Multi-factor authentication'],
                        'A firewall filters traffic from trusted and untrusted networks to keep unauthorised users out.'],
                    ['mc', 'You are leaving the office and remember an important document is still in the printer. What should you do?',
                        'Go back and keep the document in a secure place', ['Leave it there until tomorrow', 'Ask someone else to take it out', 'Email the printer team about it'],
                        'Unattended printed documents are easy for anyone to pick up.'],
                    ['tf', 'Malware can only spread through email attachments, not through social media.', false,
                        'Malware can also spread on social media, for example through shortened links hidden inside adverts.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Small habits, strong security', 'Human error is the number one cause of data breaches. Good habits close the gap.') .
                    sb_note('summary', 'In summary', sb_ul([
                        'Secure accounts with strong, unique passwords, multi-factor authentication, updates and encryption.',
                        'Your workstation is your responsibility: keep it clear, locked and yours alone.',
                        'Firewalls, antivirus and encryption work together to protect your computer.',
                        'On social media, share less, avoid clicking adverts and take care on public Wi-Fi.',
                    ])) .
                    sb_keypoints('Before you leave your desk', [
                        'Lock or log off your computer',
                        'Put sensitive papers away or shred them',
                        'Remove USB devices and take your printouts',
                        'Check that secure doors are closed',
                    ]) .
                    sb_p('Thank you for completing Cyber Security Measures.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about keeping your accounts and workstation secure?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
