<?php
// Wellbeing at Work: optional course (Health, Safety & Wellbeing). Stress, sleep, movement and breaks, boundaries with
// work messages, supporting a colleague and where to get help. Supportive, not medical advice.
return [
    'shortname' => 'WEL201',
    'fullname' => 'Wellbeing at Work',
    'category' => 'safety',
    'mandatory' => false,
    'minutes' => 30,
    'cover' => 'img/photo/wellness.jpg',
    'summary' => 'Feeling well helps you do good work and enjoy it. In 30 minutes you will look at stress and its early signs, simple habits for sleep, movement and breaks, healthy boundaries with work messages, how to support a colleague and where to get help.',
    'outcomes' => [
        'Recognise the early signs of stress in yourself and others',
        'Build small, realistic habits for sleep, movement and breaks',
        'Set healthy boundaries with work messages',
        'Start a supportive conversation with a colleague who is struggling',
        'Know where to get help, at work and outside it',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Wellbeing at Work', 'Small habits, real difference',
                        'Wellbeing is not about being happy all the time. It is about noticing how you are doing, and having a few simple tools for when things get heavy.') .
                    sb_stats([
                        ['2', 'short lessons with practical ideas you can try this week'],
                        ['3', 'places to get help: the employee assistance programme, your manager and HR'],
                        ['30 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What stress is, and the signs to look out for',
                        'Simple habits for better sleep, more movement and real breaks',
                        'How to switch off from work messages',
                        'How to support a colleague who seems to be struggling',
                        'Where to go for help, and when',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Two short lessons with ideas you can try straight away.'],
                        ['Reflect', 'Answer an anonymous poll, share a tip and write a short personal plan.'],
                        ['Check', 'Answer five questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought.'],
                    ]) .
                    sb_note('info', 'This course is not medical advice', sb_p('It shares general, practical ideas. If you are worried about your physical or mental health, please speak to a doctor or a qualified professional. If you or someone else is in immediate danger, call your local emergency number.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: signs of stress', 'package' => sb_h5p('multiple-choice-2-6', 'Signs of stress', [
                    'question' => '<p>James has been working late every night for three weeks. Which of these could be an early sign of stress?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>He is snapping at colleagues and finding it hard to concentrate.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. Irritability and poor concentration are common early signs of stress.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>He takes his usual lunch break with the team.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Keeping a normal routine is usually a good sign, not a warning sign.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>He asks a colleague for help with a task.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Asking for help is a healthy way of managing workload.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Stress and energy',
            'summary' => 'What stress is and its signs, and simple habits for sleep, movement and breaks.',
            'items' => [
                ['type' => 'page', 'name' => 'Understanding stress', 'content' =>
                    sb_banner('Lesson 1', 'Pressure is normal. Too much for too long is not.', 'A little pressure can help you focus. Stress is what happens when the demands feel bigger than your ability to cope.') .
                    sb_p('Everyone feels stressed sometimes: a deadline, a difficult conversation, a busy month. Short bursts usually pass. When stress goes on for weeks, it can affect your sleep, your health and your relationships.',
                        'Noticing the signs early gives you the best chance to do something about it.') .
                    sb_columns([
                        ['How you might feel', sb_ul(['Irritable or short-tempered', 'Anxious or on edge', 'Overwhelmed or unable to switch off', 'Low or unmotivated'])],
                        ['What you might notice', sb_ul(['Poor sleep or tiredness', 'Headaches or tense muscles', 'Trouble concentrating', 'Skipping meals or breaks', 'Withdrawing from colleagues'])],
                    ]) .
                    sb_scenario('Priya has a big project due in two weeks. She has started checking emails at midnight, skipping lunch and feels tense most of the day.',
                        'What could she try? (Ideas: talk to her manager about priorities, block time for focused work, take a proper lunch break and set a time to stop checking messages.)') .
                    sb_note('tip', 'Talk early', sb_p('If workload is the cause, tell your manager before it builds up. It is much easier to adjust priorities early than to recover from burnout later.'))],
                ['type' => 'book', 'name' => 'Sleep, movement and breaks', 'chapters' => [
                    ['Sleep', sb_banner('Lesson 1', 'Rest is part of the work', 'Good sleep helps your mood, focus and health more than almost anything else.') .
                        sb_p('Most adults need around seven to nine hours of sleep a night. Shift work, young children and long commutes can make that hard, so aim for small improvements rather than perfection.') .
                        sb_keypoints('Habits that help many people sleep', [
                            'Go to bed and get up at roughly the same time, even at weekends',
                            'Put your phone away 30 to 60 minutes before bed',
                            'Keep your bedroom cool, dark and quiet',
                            'Cut down on coffee, tea and energy drinks in the afternoon',
                            'Write tomorrow’s to-do list before bed so your mind can let go',
                        ]) .
                        sb_note('info', 'When to get advice', sb_p('If poor sleep lasts for several weeks or affects your daily life, speak to a doctor.'))],
                    ['Movement', sb_p('You do not need a gym to feel the benefit of moving more. Short bursts of activity through the day help your energy, mood and back.') .
                        sb_dodont(
                            ['Stand up and move every 30 to 60 minutes', 'Take walking meetings for one-to-one calls', 'Use the stairs for a floor or two', 'Stretch your neck, shoulders and wrists at your desk'],
                            ['Sit for hours without a break', 'Eat lunch at your desk every day', 'Wait for a free hour to exercise', 'Push through pain: stop and get advice']) .
                        sb_note('tip', 'Beat the heat', sb_p('In the hot months, walk indoors or early in the morning or evening, and drink water through the day.'))],
                    ['Real breaks', sb_p('A break is time away from work, not a different screen. Even five minutes away from your desk can help you come back clearer.') .
                        sb_steps([
                            ['Plan them', 'Put a short break in your calendar mid-morning and mid-afternoon.'],
                            ['Step away', 'Leave your desk: get a drink, go outside, talk to a colleague about something other than work.'],
                            ['Take your lunch', 'Eat away from your screen when you can.'],
                            ['Use your leave', 'Take your annual leave through the year, not all at the end.'],
                        ]) .
                        sb_quote('Almost everything will work again if you unplug it for a few minutes, including you.', 'Anne Lamott, writer')],
                ]],
            ],
        ],
        [
            'name' => 'Lesson 2 · Boundaries and support',
            'summary' => 'Switching off from work messages, supporting a colleague and where to get help.',
            'items' => [
                ['type' => 'page', 'name' => 'Healthy boundaries with work messages', 'content' =>
                    sb_banner('Lesson 2', 'Always reachable is not the same as always working', 'Clear boundaries help you rest, and help your team know what to expect.') .
                    sb_steps([
                        ['Agree what is urgent', 'Talk with your team about what really needs an answer out of hours, and how to reach each other for it, such as a phone call.'],
                        ['Turn off notifications', 'Mute work email and chat on your phone outside your working hours.'],
                        ['Set your status', 'Show your working hours in your calendar and chat profile, especially across time zones.'],
                        ['Schedule your messages', 'Writing late? Schedule the email to send in the morning so others don’t feel they must reply.'],
                    ]) .
                    sb_faq([
                        ['My manager messages me late at night. Do I have to reply?', 'Ask your manager what they expect. Many people send messages when it suits them and do not expect a reply until the next working day.'],
                        ['I work with colleagues in other time zones. How can I set boundaries?', 'Agree a few overlap hours for live calls, and use shared documents and scheduled messages for the rest.'],
                        ['What if something really is urgent?', 'Agree a clear route for real emergencies, such as a phone call, so everything else can wait.'],
                    ])],
                ['type' => 'page', 'name' => 'Supporting a colleague', 'content' =>
                    sb_banner('Lesson 2', 'You don’t need the answers', 'Just noticing and asking can make a real difference to someone who is struggling.') .
                    sb_p('You might notice a colleague seems quieter than usual, more irritable, often late or working very long hours. You do not need to be an expert. A kind, private question is a good start.') .
                    sb_dodont(
                        ['Choose a private, relaxed moment', 'Ask open questions: “How are things going, really?”', 'Listen more than you talk', 'Ask twice: people often say “fine” the first time', 'Point them to support and check in again later'],
                        ['Try to diagnose or fix everything', 'Share what they told you with others', 'Compare it with your own problems', 'Push them to talk if they are not ready']) .
                    sb_scenario('Omar notices that Fatima, usually cheerful, has been quiet for a couple of weeks and is eating lunch alone at her desk.',
                        'What could he say? (Example: “I’ve noticed you seem a bit quieter lately. Fancy a coffee? No pressure to talk, but I’m happy to listen.”)') .
                    sb_note('warn', 'If someone may be at risk', sb_p('If a colleague talks about harming themselves or you believe they are in danger, do not leave them alone. Call your local emergency number and tell your manager or HR.'))],
                ['type' => 'page', 'name' => 'Where to get help', 'content' =>
                    sb_banner('Lesson 2', 'Asking for help is a strength', 'Support is available, at work and outside it. You do not have to wait until things are bad.') .
                    sb_columns([
                        ['Employee assistance programme', sb_p('Free, confidential support by phone or online, often including counselling sessions and advice on money, family or legal worries. Check the intranet or ask HR for the contact details.')],
                        ['Your manager', sb_p('Can help with workload, priorities, working hours and time off. You choose how much you share.')],
                        ['HR', sb_p('Can explain leave, flexible working, adjustments and other support. Conversations are handled with discretion.')],
                    ]) .
                    sb_keypoints('Outside work', [
                        'Your doctor, for physical or mental health concerns',
                        'A qualified counsellor or therapist',
                        'Trusted friends, family or your community',
                        'Your local emergency number, if you or someone else is in immediate danger',
                    ]) .
                    sb_note('info', 'Confidential means confidential', sb_p('The employee assistance programme does not tell the company who uses it or what was discussed.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share ideas with colleagues, and make a small plan for yourself.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what helps you most?', 'intro' => 'Pick the habit you would most like to improve. Results are shown after you answer.',
                    'options' => ['Better sleep', 'Moving more during the day', 'Taking proper breaks', 'Switching off from work messages']],
                ['type' => 'forum', 'name' => 'Share a wellbeing tip', 'intro' => 'Share one small thing that helps you feel well at work. Keep it general: no need to share anything personal.',
                    'posts' => [['Welcome: what works for you?', 'What is one small habit that helps you through a busy week? A walk after lunch, a playlist, a no-meetings morning? Your idea could help a colleague.']]],
                ['type' => 'assign', 'name' => 'My small wellbeing plan', 'dueindays' => 21, 'words' => 150,
                    'intro' => 'Write a short, practical plan for yourself. Share only what you are comfortable with.',
                    'instructions' => sb_p('Choose two small habits from this course you would like to try over the next two weeks. Write a short plan, in 150 words or fewer.') .
                        sb_keypoints('Include', ['The two habits you will try', 'When and how you will do them', 'What might get in the way, and how you will handle it', 'Who could support you, if anyone'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Five questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Five questions on everything in this course.', 'pass' => 7,
                    'after' => ['Understanding stress', 'Sleep, movement and breaks', 'Healthy boundaries with work messages', 'Supporting a colleague', 'Where to get help'], 'questions' => [
                    ['mc', 'Which of these is a common early sign of stress?',
                        'Trouble concentrating and feeling irritable', ['Taking regular lunch breaks', 'Asking a colleague for help', 'Using your annual leave'],
                        'Poor concentration and irritability are common signs. Breaks, asking for help and taking leave are healthy habits.'],
                    ['tf', 'A real break means time away from work, not just switching to a different screen.', true,
                        'Stepping away from your desk and screen, even for five minutes, helps you come back clearer.'],
                    ['mc', 'Which habit is most likely to help you switch off after work?',
                        'Muting work notifications on your phone outside working hours', ['Checking email just before bed', 'Replying to every message straight away', 'Keeping your laptop open all evening'],
                        'Muting notifications outside your hours helps you rest. Agree a separate route for real emergencies.'],
                    ['mc', 'A colleague seems quiet and withdrawn for a few weeks. What is a good first step?',
                        'Ask privately how they are, and listen', ['Tell the team you are worried about them', 'Wait until they bring it up', 'Tell them what they should do to feel better'],
                        'A private, kind question and real listening is a good start. You do not need to have the answers.'],
                    ['tf', 'The employee assistance programme tells your manager when you use it.', false,
                        'The employee assistance programme is confidential. The company is not told who uses it or what was discussed.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Notice, adjust, reach out', 'Three steps for looking after yourself and the people around you.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Notice</strong> the early signs of stress in yourself and others.',
                        '<strong>Adjust</strong> with small habits: sleep, movement, real breaks and clear boundaries.',
                        '<strong>Reach out</strong> to a colleague who is struggling, and ask for help when you need it.',
                    ])) .
                    sb_p('Thank you for completing Wellbeing at Work. The employee assistance programme, your manager and HR are always there if you need them.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for you?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How likely are you to try one of the habits from this course?', ['Very likely', 'Likely', 'Not sure', 'Unlikely']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
