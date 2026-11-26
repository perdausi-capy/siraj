<?php
// Communication Skills (Skills): built around the existing Articulate Rise lesson (Module05_CommunicationSkills).
return [
    'shortname' => 'Communication Skills',
    'fullname' => 'Communication Skills',
    'category' => 'skills',
    'mandatory' => false,
    'minutes' => 30,
    'summary' => 'Good communication builds trust, improves teamwork and gets better results. This course covers how communication works, the barriers that get in the way and the techniques that help your message land.',
    'outcomes' => [
        'Describe the communication process from sender to feedback',
        'Tell apart verbal, non-verbal, written and visual communication',
        'Identify the five barriers to communication and how to overcome them',
        'Use active listening and practical techniques to communicate clearly',
        'Check a message against the seven Cs of communication',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Communication Skills', 'Say it so it’s understood',
                        'The goal of all communication is mutual understanding. When it is missing, the result is disagreement and disappointment, at work and at home.') .
                    sb_stats([
                        ['4', 'types of communication: verbal, non-verbal, written and visual'],
                        ['7', 'Cs that make a message effective'],
                        ['30 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'The six elements of the communication process',
                        'The four types of communication, with everyday examples',
                        'The five barriers to communication and how to overcome them',
                        'Techniques and active listening habits that make you easier to understand',
                        'The seven Cs of effective communication',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through the interactive lesson. It has short knowledge checks along the way.'],
                        ['Review', 'Read the key takeaways page to fix the main ideas.'],
                        ['Share', 'Tell colleagues about a communication barrier you have faced.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                    ]) .
                    sb_note('tip', 'Try it today', sb_p('Pick one technique from the lesson, such as giving your full attention when someone speaks, and use it in your next conversation at work.'))],
            ],
        ],
        [
            'name' => 'Lesson',
            'summary' => 'The full interactive lesson, followed by its key takeaways.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Module05_CommunicationSkills', 'name' => 'Communication skills at work',
                    'intro' => 'An interactive lesson on the communication process, the types of communication, common barriers and the skills that make communication work.'],
                ['type' => 'page', 'name' => 'Key takeaways', 'content' =>
                    sb_banner('After the lesson', 'Clear message, clear feedback', 'The ideas worth keeping from the lesson, on one page.') .
                    '<h3>The communication process</h3>' .
                    sb_steps([
                        ['Sender', 'The person who starts the conversation and forms the idea to share.'],
                        ['Encoding', 'Turning the idea into words, symbols or gestures.'],
                        ['Message', 'The idea itself, spoken, written or shown through body language. It must be clear.'],
                        ['Receiver', 'The person the message is for.'],
                        ['Decoding', 'The receiver interprets the message. It works only when they understand it as the sender intended.'],
                        ['Feedback', 'The receiver’s response. Without it, the process is not complete.'],
                    ]) .
                    sb_columns([
                        ['Five barriers', sb_ul(['<strong>Physical:</strong> distance, such as teams in different locations', '<strong>Language:</strong> words the receiver doesn’t understand',
                            '<strong>Psychological:</strong> stress, anxiety, anger or low self-esteem', '<strong>Physiological:</strong> the receiver’s physical condition, such as poor hearing',
                            '<strong>Perceptual:</strong> assumptions, personality differences or resistance to change'])],
                        ['Overcoming them', sb_ul(['Acknowledge differences and gaps in people’s needs', 'Collect feedback to see what to improve',
                            'Keep messages consistent with each other and with company policy'])],
                    ]) .
                    sb_keypoints('The seven Cs of communication', [
                        '<strong>Clear:</strong> know your purpose, use simple words, avoid jargon',
                        '<strong>Concise</strong> and <strong>concrete:</strong> precise, useful information',
                        '<strong>Correct:</strong> credible, reliable and free of errors',
                        '<strong>Coherent:</strong> a logical flow and a consistent tone',
                        '<strong>Complete:</strong> everything the receiver needs to reach your conclusion',
                        '<strong>Courteous:</strong> polite, professional and respectful',
                    ]) .
                    sb_note('tip', 'Listen first', sb_p('You can only fully understand someone when you listen attentively. Good listening improves your work, your relationships and your ability to influence and negotiate.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share an experience with your colleagues.',
            'items' => [
                ['type' => 'forum', 'name' => 'Share a communication barrier', 'intro' => 'Describe a time a message got lost or misunderstood at work, and what helped. Leave out names and confidential details.',
                    'posts' => [['Welcome: which barrier got in the way?', 'Think of a misunderstanding at work. Was it a physical, language, psychological, physiological or perceptual barrier? What did you, or could you, do to overcome it?']]],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on the lesson.', 'pass' => 7,
                    'after' => ['Communication skills at work', 'Key takeaways'], 'questions' => [
                    ['mc', 'In the communication process, what is decoding?',
                        'The receiver interpreting the sender’s message', ['The sender turning information into a message', 'The receiver’s response to the message', 'The idea the sender wants to share'],
                        'Decoding is when the receiver interprets the message. Communication works only when they understand it as the sender intended.'],
                    ['mc', 'Books, emails, reports and instant text messages are examples of which type of communication?',
                        'Written', ['Verbal', 'Visual', 'Non-verbal'],
                        'Anything you write or type is written communication.'],
                    ['mc', 'Which of these is NOT an example of non-verbal communication?',
                        'Letters', ['Body movements', 'Hand signals', 'Eye contact'],
                        'Letters are written communication. Gestures, posture and eye contact are non-verbal.'],
                    ['mc', 'Your team works across different locations and can’t meet face to face. Which type of barrier is this?',
                        'Physical barrier', ['Language barrier', 'Perceptual barrier', 'Psychological barrier'],
                        'Geographical distance prevents personal interaction, which makes it a physical barrier.'],
                    ['tf', 'A person with poor hearing can be a barrier to communication.', true,
                        'This is a physiological barrier: the receiver’s physical condition affects how well they hear the message, especially with background noise.'],
                    ['tf', 'It is fine to use jargon and impressive words, because they show your audience you are an expert.', false,
                        'Clarity comes first. Use simple words everybody understands, be concise and avoid jargon.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Aim for mutual understanding', 'A message only works when the other person understands it the way you meant it.') .
                    sb_note('summary', 'In summary', sb_ul([
                        'Communication runs from sender to receiver, and is complete only when there is feedback.',
                        'Use the right type for the job: verbal, non-verbal, written or visual.',
                        'Watch for physical, language, psychological, physiological and perceptual barriers.',
                        'Check important messages against the seven Cs.',
                    ])) .
                    sb_keypoints('Habits to keep', [
                        'Give the speaker your full attention',
                        'Use simple words and avoid jargon',
                        'Ask for feedback and accept criticism without taking it personally',
                        'Use “we” and “us” rather than “I” and “you”',
                    ]) .
                    sb_p('Thank you for completing Communication Skills.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about communicating clearly at work?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
