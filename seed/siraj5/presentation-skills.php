<?php
// Presentation Skills (Skills): built around the existing Articulate Rise lesson (PresentationSkills).
return [
    'shortname' => 'Presentation Skills',
    'fullname' => 'Presentation Skills',
    'category' => 'skills',
    'mandatory' => false,
    'minutes' => 30,
    'summary' => 'Learn how to plan, prepare and deliver a presentation that holds your audience. You will look at the purposes and styles of presentations, the elements of a great one, the mistakes to avoid and the basics of effective public speaking.',
    'outcomes' => [
        'Explain what a presentation is and name its four basic purposes',
        'Recognise the six presentation styles and pick one that suits your audience',
        'Prepare a presentation using the four key elements: preparation, confidence, setting and visual aids',
        'Avoid the most common presentation mistakes',
        'Use word choice, body language and voice to speak with confidence',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Presentation Skills', 'Speak with clarity and confidence',
                        'Most people feel nervous before they present. Good preparation and knowing your subject are what turn nerves into a clear, confident talk.') .
                    sb_stats([
                        ['4', 'basic purposes of a presentation: inform, instruct, persuade and assist decisions'],
                        ['6', 'presentation styles to choose from'],
                        ['30 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What a presentation is and why businesses use them',
                        'The six presentation styles, and how to match one to your audience',
                        'The four key elements of a great presentation',
                        'Why preparation matters, and the mistakes that weaken a talk',
                        'The three elements of effective speaking: word choice, body language and voice',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through the interactive lesson. It has short knowledge checks along the way.'],
                        ['Review', 'Read the key takeaways page to fix the main ideas.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Read the summary and tell us what you thought of the course.'],
                    ]) .
                    sb_note('tip', 'Make it real', sb_p('Think of a presentation you have coming up, or one you gave recently. Apply each idea to it as you go.'))],
            ],
        ],
        [
            'name' => 'Lesson',
            'summary' => 'The full interactive lesson, followed by its key takeaways.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'PresentationSkills', 'name' => 'Presentation skills: plan, prepare and deliver',
                    'intro' => 'An interactive lesson on presentation purposes and styles, preparation, common mistakes and public speaking.'],
                ['type' => 'page', 'name' => 'Key takeaways', 'content' =>
                    sb_banner('After the lesson', 'Prepare well, speak simply', 'The ideas worth keeping from the lesson, on one page.') .
                    sb_columns([
                        ['Four purposes', sb_ul(['<strong>Inform:</strong> share news or basic information', '<strong>Instruct:</strong> give directions or teach a skill',
                            '<strong>Persuade:</strong> win support for a proposal or a deal', '<strong>Assist decisions:</strong> help a group decide on rules or operations'])],
                        ['Six styles', sb_ul(['<strong>Coach:</strong> passion for the subject', '<strong>Connector:</strong> what you share with the audience',
                            '<strong>Freeform:</strong> little planning, clear message', '<strong>Instructor:</strong> complex information',
                            '<strong>Storytelling:</strong> stories that support key ideas', '<strong>Visual:</strong> big, vibrant slides with little text'])],
                    ]) .
                    sb_keypoints('Four key elements of a great presentation', [
                        '<strong>Preparation:</strong> time to gather, organise and perfect your material',
                        '<strong>Confidence:</strong> knowing your subject as thoroughly as possible',
                        '<strong>Setting:</strong> a comfortable place where the audience can focus',
                        '<strong>Visual aids:</strong> slides that support your message, not distract from it',
                    ]) .
                    sb_dodont(
                        ['Rehearse as much as you can', 'Use short bullet points to introduce a topic', 'Explain and expand on your slides', 'Finish on time, or early'],
                        ['Arrive under-prepared', 'Fill slides with text', 'Read your slides word for word', 'Go beyond your time limit']) .
                    sb_note('tip', 'Calm your voice before you start', sb_p('Stand comfortably with relaxed shoulders. Breathe in deeply through your nose and out through your mouth, with a hand on your abdomen to feel the movement. Deep, steady breathing gives you control of your voice and calms the mind.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Think about your own presenting style.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: which style is closest to yours?', 'intro' => 'No style is better than another, but the wrong style for an audience can cause problems. Pick the one that sounds most like you. Results are shown after you answer.',
                    'options' => ['Coach', 'Connector', 'Freeform', 'Instructor', 'Storytelling', 'Visual']],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on the lesson.', 'pass' => 7,
                    'after' => ['Presentation skills: plan, prepare and deliver', 'Key takeaways'], 'questions' => [
                    ['mc', 'You are presenting to a potential client to offer a service and close a deal. What is the purpose of this presentation?',
                        'To persuade', ['To inform', 'To instruct', 'To entertain'],
                        'A persuasive presentation tries to get the audience to accept a proposal or make a deal.'],
                    ['mc', 'Which presentation style uses huge, vibrant slides with very little text?',
                        'Visual style', ['Coach style', 'Instructor style', 'Freeform style'],
                        'Visual presenters use slides only to enhance their talk, with big images and little text.'],
                    ['mc', 'What are the four key elements of a great presentation?',
                        'Preparation, confidence, setting and visual aids', ['Preparation, confidence, setting and style', 'Mood, confidence, setting and visual aids', 'Preparation, patience, setting and visual aids'],
                        'These four elements help a presentation hold the audience’s interest and get the information across.'],
                    ['tf', 'Reading your slides word for word is a good way to make sure you don’t miss anything.', false,
                        'Your audience can read the slides themselves. Slides are a visual aid: your job is to explain and expand on them.'],
                    ['mc', 'What are the three elements of effective speaking?',
                        'Word choice, body language and voice', ['Word choice, mannerism and voice', 'Voice, body language and accent', 'Word choice, body language and diction'],
                        'What you say, how you stand and move, and how you control your voice all shape how your message lands.'],
                    ['tf', 'Good preparation and knowing your subject well can help reduce the fear of speaking in public.', true,
                        'Preparation builds confidence and helps you avoid mistakes caused by nerves.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Know your purpose, know your audience', 'Everything else in a good presentation follows from these two.') .
                    sb_note('summary', 'In summary', sb_ul([
                        'A presentation communicates information from a speaker to an audience, to inform, instruct, persuade or help a decision.',
                        'Choose a style that suits your audience. No style is better than another.',
                        'Planning and preparing ahead of time is what makes an excellent presentation.',
                        'Keep slides simple, don’t read them out, and respect your time limit.',
                    ])) .
                    sb_keypoints('Before your next presentation', [
                        'Find out who your audience is and what they need',
                        'Rehearse until you only need to glance at your notes',
                        'Use simple words and short sentences',
                        'Take a few deep breaths before you begin',
                    ]) .
                    sb_p('Thank you for completing Presentation Skills.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about giving a presentation?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
