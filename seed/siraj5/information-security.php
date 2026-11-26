<?php
// Information Security (Digital): built around the existing Articulate Rise lesson (InformationSecurity) and its quiz.
return [
    'shortname' => 'Information Security',
    'fullname' => 'Information Security',
    'category' => 'digital',
    'mandatory' => false,
    'minutes' => 35,
    'summary' => 'Information is one of an organisation’s most valued assets. This course shows you how to keep it confidential, accurate and available, spot the threats against it and handle sensitive data safely.',
    'outcomes' => [
        'Explain the CIA triad: confidentiality, integrity and availability',
        'Recognise common threats, including malware and social engineering',
        'Classify information and protect sensitive data',
        'Describe how physical and virtual access controls keep data safe',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Information Security', 'Protect what the organisation runs on',
                        'Human error is the leading cause of data breaches. Knowing the threats and a few good habits prevents most of them.') .
                    sb_stats([
                        ['3', 'principles of the CIA triad: confidentiality, integrity and availability'],
                        ['10', 'questions in the final quiz'],
                        ['35 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What information security is and why it matters',
                        'The CIA triad, and its opposite: disclosure, alteration and destruction',
                        'The types of threat, from malware to phishing',
                        'How to classify and protect sensitive information',
                        'How access control limits who gets in, physically and online',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through the interactive lesson in four parts, with short knowledge checks along the way.'],
                        ['Review', 'Read the key takeaways page to fix the main ideas.'],
                        ['Check', 'Answer ten questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Read the summary and tell us what you thought of the course.'],
                    ]) .
                    sb_note('tip', 'Look around you', sb_p('As you go, think about the information you handle each day: what is public, what is confidential, and where it is kept.'))],
            ],
        ],
        [
            'name' => 'Lesson',
            'summary' => 'The full interactive lesson, followed by its key takeaways.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'InformationSecurity', 'name' => 'Information security essentials',
                    'intro' => 'An interactive lesson in four parts: information security and the CIA triad, security threats, protecting sensitive information, and access control.'],
                ['type' => 'page', 'name' => 'Key takeaways', 'content' =>
                    sb_banner('After the lesson', 'Confidential, intact, available', 'The ideas worth keeping from the lesson, on one page.') .
                    sb_columns([
                        ['The CIA triad', sb_ul(['<strong>Confidentiality:</strong> protect data from unauthorised access', '<strong>Integrity:</strong> protect data from being deleted or changed, by accident or on purpose',
                            '<strong>Availability:</strong> keep data and services accessible to authorised users'])],
                        ['Its opposite: DAD', sb_ul(['<strong>Disclosure:</strong> unauthorised access to confidential data', '<strong>Alteration:</strong> data has been changed',
                            '<strong>Destruction:</strong> data or a whole system can’t be reached'])],
                    ]) .
                    '<h3>Common malicious attacks</h3>' .
                    sb_columns([
                        ['Malware', sb_ul(['<strong>Virus:</strong> spreads and infects files', '<strong>Trojan:</strong> disguised as a legitimate file', '<strong>Worm:</strong> copies itself across devices',
                            '<strong>Adware:</strong> unwanted pop-up adverts', '<strong>Spyware:</strong> monitors you and steals information'])],
                        ['Social engineering', sb_ul(['<strong>Phishing:</strong> emails or texts that seem to come from a trusted source', '<strong>Vishing:</strong> voice calls or voice messages',
                            '<strong>Smishing:</strong> text message scams'])],
                    ]) .
                    sb_keypoints('Four classes of information', [
                        '<strong>Public:</strong> approved for public access',
                        '<strong>Private:</strong> for use within the organisation only',
                        '<strong>Confidential:</strong> for authorised staff only',
                        '<strong>Restricted:</strong> highly sensitive, such as ID or card numbers, with serious risk if leaked',
                    ]) .
                    sb_dodont(
                        ['Lock your screen or log off when you leave', 'Shred sensitive documents you no longer need', 'Double-check recipients before sending an email', 'Keep sensitive papers in locked cabinets'],
                        ['Share or write down passwords', 'Leave documents, keys or USB sticks unattended', 'Disclose more information than needed', 'Ignore a stranger in a restricted area']) .
                    sb_note('warn', 'Signs of a denial-of-service attack', sb_p('A slow computer or network, websites that don’t load properly and a sudden loss of connection can all mean systems are under attack. Report them.'))],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Ten questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'keep', 'mod' => 'quiz', 'match' => 'INFo Quiz', 'name' => 'Final check',
                    'intro' => 'Ten questions on the lesson. You need 7 out of 10 to pass, and you can try again.',
                    'after' => ['Information security essentials', 'Key takeaways']],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Security is a daily habit', 'Most breaches start with a human mistake, and most can be stopped by one.') .
                    sb_note('summary', 'In summary', sb_ul([
                        'Information security keeps data confidential, intact and available, so the organisation can function safely.',
                        'Threats can be deliberate or accidental, and come from outside or inside the organisation.',
                        'The two common types of malicious attack are malware and social engineering.',
                        'Classify information, limit who can see it and dispose of it securely.',
                    ])) .
                    sb_keypoints('Every day', [
                        'Check the sender before you click a link or open an attachment',
                        'Verify suspicious messages through official contact details',
                        'Encrypt confidential information sent over the internet',
                        'Never leave sensitive information unattended',
                    ]) .
                    sb_p('Thank you for completing Information Security.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about protecting sensitive information?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
