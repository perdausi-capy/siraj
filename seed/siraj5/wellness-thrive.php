<?php
// Wellness: Be Well and Thrive (imported from siraj5 as 'Wellness : Be Well and Thrive Program'). The full programme:
// five SCORM modules in English, a final check and a wrap-up. Applied by seed/siraj5_restructure.php.
return [
    'shortname' => 'Wellness : Be Well and Thrive Program',
    'fullname' => 'Wellness: Be Well and Thrive',
    'category' => 'safety',
    'mandatory' => false,
    'minutes' => 60,
    'summary' => 'Wellness is the daily choices that help you move from just coping to thriving. In five short modules you will explore what wellness means, practise gratitude, build better habits, calm your nervous system and train your brain towards the good.',
    'outcomes' => [
        'Explain what wellness is and name the 8 dimensions of wellness',
        'Use simple daily gratitude practices, and avoid the pitfalls of forced positivity',
        'Set personal non-negotiables and spot what drains and restores your energy',
        'Recognise your nervous system state and bring yourself back into your window of tolerance',
        'Use focus, movement, breathing and mindfulness to rewire your brain for wellbeing',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this programme covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Be Well and Thrive', 'From coping to thriving', 'Health is the goal. Wellness is how you get there: the small, positive choices you make every day.') .
                    sb_stats([
                        ['5', 'short modules, from the meaning of wellness to rewiring your brain'],
                        ['8', 'dimensions of wellness to check in with'],
                        ['60 min', 'to complete the programme, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What wellness really means, and the 8 dimensions that shape it',
                        'How gratitude affects your brain, your sleep and your stress',
                        'How to set non-negotiables and manage your energy',
                        'How your nervous system responds to stress, and how to calm it',
                        'How to use neuroplasticity to build healthier habits of mind',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through five interactive modules. Each one opens with a short video and ends with a quick review.'],
                        ['Reflect', 'Each module includes check-ins and questions to ask yourself. Take a moment to answer them honestly.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Read the summary and tell us what you thought.'],
                    ]) .
                    sb_note('tip', 'Go at your own pace', sb_p('You don’t have to finish in one sitting. Your place is saved, so you can do one module a day and try the tips as you go.')) .
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
            'name' => 'Module 4 · Nervous system training for resilience',
            'summary' => 'Your gas and brake pedals, the vagus nerve, the window of tolerance and the flow cycle.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Module04_NervousSystemTrainingForResilience_EN', 'name' => 'Module 4: Nervous system training for resilience',
                    'intro' => 'Understand how stress affects your nervous system, how to tone the vagus nerve, how to stay within your window of tolerance and how to get into flow.'],
            ],
        ],
        [
            'name' => 'Module 5 · Rewire your brain',
            'summary' => 'Neuroplasticity, the negativity bias, and how body, breath and mind can rewire you for wellbeing.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'Module05_RewireYourBrainForHealthAndHappiness_EN', 'name' => 'Module 5: Rewire your brain for health and happiness',
                    'intro' => 'Learn how your brain changes with use, how to balance the negativity bias, and how movement, better breathing and mindfulness help you rewire.'],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'A quick check-in on your own wellness.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: which dimension needs your attention?', 'intro' => 'Module 1 asks: what areas require attention? Pick the dimension of wellness you would most like to work on. Results are shown after you answer.',
                    'options' => ['Physical', 'Intellectual', 'Emotional', 'Social', 'Spiritual', 'Occupational', 'Financial', 'Environmental']],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions, one or two from each module.', 'pass' => 7,
                    'after' => ['Module 1: What is wellness?', 'Module 2: Gratitude for less stress and more success', 'Module 3: Build better wellbeing',
                        'Module 4: Nervous system training for resilience', 'Module 5: Rewire your brain for health and happiness'],
                    'questions' => [
                    ['mc', 'How does this course describe the link between health and wellness?',
                        'Health is the goal, and wellness is the means of achieving it', ['They mean exactly the same thing', 'Wellness is a type of medical treatment', 'Wellness is simply the absence of illness'],
                        'Health is a state of being. Wellness is the active process of intentions, actions and choices that helps you reach it.'],
                    ['tf', 'On the Illness-Wellness Continuum, the direction you are facing matters more than where you are right now.', true,
                        'Wherever you are on the continuum, the most important thing is whether you are facing towards wellness or away from it.'],
                    ['mc', 'Research in Module 2 found that expressing gratitude makes the brain release which neurotransmitter?',
                        'Dopamine', ['Adrenaline', 'Cortisol', 'Insulin'],
                        'Gratitude releases dopamine, which is linked to pleasure, motivation, reward and attention. It also helps lower the stress hormone cortisol.'],
                    ['tf', 'If doing your non-negotiable would harm your physical or mental health in that moment, it is fine to pass on it.', true,
                        'Non-negotiables are there to support you, not cause harm. You can be kind to yourself and still stay accountable.'],
                    ['mc', 'In the car analogy from Module 4, which part of the nervous system acts as the brake?',
                        'The parasympathetic nervous system', ['The sympathetic nervous system', 'The fight-or-flight response', 'Stress hormones such as adrenaline'],
                        'The sympathetic system is the gas that speeds you up. The parasympathetic system is the brake that lets you rest, digest and recover.'],
                    ['mc', 'What does the S.E.N.D. breathing principle stand for?',
                        'Slow, Even, Nose, Diaphragm', ['Stop, Exhale, Notice, Decide', 'Sit, Empty, Nose, Deepen', 'Steady, Easy, Natural, Daily'],
                        'Slow your breath, even it out, breathe in and out through your nose, and breathe deeply using your diaphragm.'],
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
                        '<strong>Your nervous system</strong> needs both gas and brake. Learn to slow down and recover.',
                        '<strong>What you focus on grows.</strong> Practice, persistence and patience rewire the brain.',
                    ])) .
                    sb_keypoints('Try this week', [
                        'In the evening, note 3 things that went well today, and why',
                        'Write down two or three non-negotiables and put the list where you will see it',
                        'Check your breathing a few times a day: slow, even, through the nose, from the diaphragm',
                        'When something good happens, savour it for 10 to 30 seconds',
                    ]) .
                    sb_quote('When we show up to the present moment with all of our senses, we invite the world to fill us with joy.', 'From the Be Well and Thrive programme') .
                    sb_p('Thank you for completing Be Well and Thrive. Your certificate appears on the course page and in Achievements once every step is done.')],
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
