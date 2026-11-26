<?php
// Wellness Essentials (imported from siraj5 as 'Wellness Course'). A short starter: Modules 1 to 3 of the wellness
// programme in English, a final check and a wrap-up. Applied by seed/siraj5_restructure.php.
return [
    'shortname' => 'Wellness Course',
    'fullname' => 'Wellness Essentials',
    'category' => 'safety',
    'mandatory' => false,
    'minutes' => 35,
    'summary' => 'A short starter on looking after yourself. In three modules you will learn what wellness really means, how gratitude lowers stress, and how to build better wellbeing with simple daily habits.',
    'outcomes' => [
        'Explain the difference between health and wellness',
        'Name the 8 dimensions of wellness and check in with each one',
        'Use simple daily gratitude practices, and avoid the pitfalls of forced positivity',
        'Set personal non-negotiables and spot what drains and restores your energy',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this short course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Wellness Essentials', 'A good place to start', 'Wellness is the positive, everyday choices that help you feel well and do well. This course gives you the basics.') .
                    sb_stats([
                        ['3', 'short modules: wellness, gratitude and better wellbeing'],
                        ['8', 'dimensions of wellness to check in with'],
                        ['35 min', 'to complete the course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What wellness means, and how it differs from health',
                        'The Illness-Wellness Continuum and the 8 dimensions of wellness',
                        'How gratitude affects your brain, sleep and stress, and how to practise it',
                        'How to set non-negotiables, grow your mindset and manage your energy',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through three interactive modules. Each one opens with a short video and ends with a quick review.'],
                        ['Reflect', 'Each module includes check-ins and questions to ask yourself. Take a moment to answer them honestly.'],
                        ['Check', 'Answer five questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Read the summary and tell us what you thought.'],
                    ]) .
                    sb_note('tip', 'Want to go further?', sb_p('These are the first three modules of the full Be Well and Thrive programme. The full programme adds nervous system training and rewiring your brain.')) .
                    sb_note('info', 'Looking after yourself', sb_p('This course offers general wellbeing tips. It is not medical advice. If you are struggling, please talk to your doctor or someone you trust.'))],
            ],
        ],
        [
            'name' => 'Module 1 · What is wellness?',
            'summary' => 'The meaning of wellness, the Illness-Wellness Continuum and the 8 dimensions of wellness.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Module01_WhatIsWellness_EN', 'name' => 'Module 1: What is wellness?',
                    'intro' => 'Learn the difference between health and wellness, where you sit on the Illness-Wellness Continuum, and how the 8 dimensions of wellness influence each other.'],
            ],
        ],
        [
            'name' => 'Module 2 · Gratitude for less stress',
            'summary' => 'What gratitude is, what it does to your brain and body, and how to practise it every day.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Module02_GratitudeforLessStress_EN', 'name' => 'Module 2: Gratitude for less stress and more success',
                    'intro' => 'Explore the science of gratitude, its benefits for sleep, stress and outlook, the pitfalls to avoid, and simple daily gratitude tips.'],
            ],
        ],
        [
            'name' => 'Module 3 · Build better wellbeing',
            'summary' => 'Non-negotiables, a growth mindset, self-esteem, and your energy drainers and gainers.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Module03_Betterwellbeing', 'name' => 'Module 3: Build better wellbeing',
                    'intro' => 'Create your own set of non-negotiables, learn the difference between a fixed and a growth mindset, and find out what drains and restores your energy.'],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Five questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Five questions on the three modules.', 'pass' => 7,
                    'after' => ['Module 1: What is wellness?', 'Module 2: Gratitude for less stress and more success', 'Module 3: Build better wellbeing'],
                    'questions' => [
                    ['mc', 'How does this course describe the link between health and wellness?',
                        'Health is the goal, and wellness is the means of achieving it', ['They mean exactly the same thing', 'Wellness is a type of medical treatment', 'Wellness is simply the absence of illness'],
                        'Health is a state of being. Wellness is the active process of intentions, actions and choices that helps you reach it.'],
                    ['tf', 'On the Illness-Wellness Continuum, the direction you are facing matters more than where you are right now.', true,
                        'Wherever you are on the continuum, the most important thing is whether you are facing towards wellness or away from it.'],
                    ['mc', 'Research in Module 2 found that expressing gratitude makes the brain release which neurotransmitter?',
                        'Dopamine', ['Adrenaline', 'Cortisol', 'Insulin'],
                        'Gratitude releases dopamine, which is linked to pleasure, motivation, reward and attention. It also helps lower the stress hormone cortisol.'],
                    ['tf', 'A good gratitude practice means pushing aside feelings like sadness or anxiety and focusing only on the positive.', false,
                        'All emotions are valid. Process what you feel first, and never force gratitude. You can feel grateful and still have difficult emotions.'],
                    ['mc', 'Which of these is typical of a growth mindset?',
                        'Learning from constructive criticism', ['Avoiding challenges to stay safe', 'Giving up quickly when things get hard', 'Resenting the success of others'],
                        'A growth mindset embraces challenges, learns from feedback and is inspired by others’ success. It feels free and expansive.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Small choices, every day', 'Wellness is not a fixed state. It is something you keep choosing.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Wellness</strong> is holistic: 8 dimensions that influence each other. Check in with them regularly.',
                        '<strong>Gratitude</strong> is a simple, free practice that helps lower stress and improve sleep and outlook.',
                        '<strong>Non-negotiables</strong> protect your health when life gets busy. Keep them realistic and kind.',
                        '<strong>A growth mindset</strong> and knowing your energy drainers and gainers help you thrive.',
                    ])) .
                    sb_keypoints('Try this week', [
                        'Ask yourself: what areas are going well, and what areas need attention?',
                        'In the evening, note 3 things that went well today, and why',
                        'Write down two or three non-negotiables and put the list where you will see it',
                        'Celebrate the small wins, and speak to yourself as you would to a friend',
                    ]) .
                    sb_quote('When we show up to the present moment with all of our senses, we invite the world to fill us with joy.', 'From the Be Well and Thrive programme') .
                    sb_p('Thank you for completing Wellness Essentials. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily life?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How likely are you to try one of the practices this week?', ['Very likely', 'Likely', 'Not sure', 'Unlikely']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
