<?php
// Introduction to Cyber Security (Digital): built around the existing Articulate Rise lesson (Introduction_to_Cyber_Security).
return [
    'shortname' => 'Introduction to Cyber Security',
    'fullname' => 'Introduction to Cyber Security',
    'category' => 'digital',
    'mandatory' => false,
    'minutes' => 25,
    'summary' => 'Digital work brings new opportunities and new risks. This first part of the cyber security series explains what cyber security is, how the CIA triad guides it and how to spot and avoid phishing, vishing and smishing.',
    'outcomes' => [
        'Explain what cyber security is and why it matters',
        'Describe the three parts of the CIA triad',
        'Recognise phishing, vishing and smishing attacks',
        'Follow simple safety tips to avoid data breaches and information theft',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Introduction to Cyber Security', 'Don’t be a victim',
                        'Scammers don’t need to break into systems if they can trick a person instead. Knowing their tactics is the first step to stopping them.') .
                    sb_stats([
                        ['3', 'types of social engineering: phishing, vishing and smishing'],
                        ['4', 'warning signs of a phishing email'],
                        ['25 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What cyber security is and why it matters',
                        'The CIA triad: confidentiality, integrity and availability',
                        'What social engineering is',
                        'How to spot phishing, vishing and smishing',
                        'Simple safety tips that prevent data breaches',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through the interactive lesson. It includes short scenarios where you choose what to do.'],
                        ['Review', 'Read the key takeaways page to fix the main ideas.'],
                        ['Share', 'Tell colleagues about a scam message or call you have received.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                    ]) .
                    sb_note('tip', 'What comes next', sb_p('This is part one of the series. Cyber Security Measures follows on with practical ways to protect your accounts, workstation and devices.'))],
            ],
        ],
        [
            'name' => 'Lesson',
            'summary' => 'The full interactive lesson, followed by its key takeaways.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Introduction_to_Cyber_Security', 'name' => 'Cyber security and social engineering',
                    'intro' => 'An interactive lesson on what cyber security is, the CIA triad and the social engineering tactics scammers use, with scenarios to practise on.'],
                ['type' => 'page', 'name' => 'Key takeaways', 'content' =>
                    sb_banner('After the lesson', 'Spot the trick, stop the attack', 'The ideas worth keeping from the lesson, on one page.') .
                    sb_keypoints('The CIA triad', [
                        '<strong>Confidentiality:</strong> sensitive and private information is protected from unauthorised access',
                        '<strong>Integrity:</strong> data is protected from deletion, changes or alterations',
                        '<strong>Availability:</strong> data and services are up and running when needed',
                    ]) .
                    '<h3>Four signs of a phishing email</h3>' .
                    sb_steps([
                        ['A suspicious sender address', 'It looks like a real company’s address, with a few characters changed or missing.'],
                        ['Generic greetings and signature', 'No name for you, and no contact details in the signature.'],
                        ['Poor spelling and format', 'Bad grammar, spelling mistakes and inconsistent layout.'],
                        ['Links and attachments', 'Shortened links that hide the real address, and urgent pressure to open a file.'],
                    ]) .
                    sb_columns([
                        ['Vishing (voice)', sb_ul(['A sense of urgency or threats', 'Requests to confirm personal or account details', 'Offers that are too good to be true'])],
                        ['Smishing (text)', sb_ul(['Never click links or download files from unknown sources', 'Beware of investment offers by text', 'Don’t reply to suspicious offers'])],
                    ]) .
                    sb_note('tip', 'Check it the safe way', sb_p('Unsure if an email is real? Contact the company directly using details from its official website, never the contact details in the message.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share what you have seen.',
            'items' => [
                ['type' => 'forum', 'name' => 'Share a scam you have spotted', 'intro' => 'Describe a phishing email, scam call or suspicious text you received and what gave it away. Never include links, phone numbers or personal data.',
                    'posts' => [['Welcome: phishing, vishing or smishing?', 'Tell us about a scam message or call you received. Which type was it, and which warning sign made you stop? Your example could help a colleague spot the next one.']]],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on the lesson.', 'pass' => 7,
                    'after' => ['Cyber security and social engineering', 'Key takeaways'], 'questions' => [
                    ['mc', 'What does CIA stand for in the CIA triad?',
                        'Confidentiality, integrity and availability', ['Confidentiality, integrity and accessibility', 'Control, integrity and availability', 'Confidentiality, investment and availability'],
                        'The CIA triad guides an organisation’s cyber security policies and sets the goals for data protection.'],
                    ['mc', 'Which social engineering attack uses voice calls or voice messages to collect sensitive information?',
                        'Vishing', ['Phishing', 'Smishing', 'Malware'],
                        'Vishing is also known as voice phishing.'],
                    ['tf', 'Smishing is also known as voice phishing.', false,
                        'Smishing is a text message scam, also known as SMS phishing. Voice phishing is vishing.'],
                    ['mc', 'Which of these is a warning sign of a phishing email?',
                        'A generic greeting and no contact details in the signature', ['The sender addresses you by your full name', 'The email comes from an address you already know and trust', 'The message includes the company’s official phone number'],
                        'Legitimate organisations usually address you by name and give their contact details.'],
                    ['mc', 'A caller says there is a problem with your online banking and asks you to verify your details. What should you do?',
                        'Give out no information and hang up', ['Confirm your details so the problem is fixed', 'Give only your account number', 'Ask the caller to text you instead'],
                        'Urgency and a request for personal information are signs of vishing.'],
                    ['tf', 'If you are unsure whether an email is real, you should contact the company using the phone number in the email.', false,
                        'Use the contact details on the company’s official website, never the ones in the message.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Pause before you respond', 'Social engineering works by rushing you. Taking a moment is your best defence.') .
                    sb_note('summary', 'In summary', sb_ul([
                        'Cyber security protects computer systems, networks, devices, programs and data from threats, damage and unauthorised access.',
                        'Human error is one of the biggest causes of security breaches.',
                        'The CIA triad stands for confidentiality, integrity and availability.',
                        'Social engineering manipulates emotions. Watch for phishing, vishing and smishing.',
                    ])) .
                    sb_keypoints('Safety tips', [
                        'Never give personal or financial information to unknown people',
                        'Check that a website address starts with https and shows a closed padlock',
                        'Use antivirus protection and a firewall',
                        'Always log out of your accounts',
                    ]) .
                    sb_p('Thank you for completing Introduction to Cyber Security.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about spotting a scam message or call?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
