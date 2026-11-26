<?php
// Presenting with Confidence: planning, structure, slides, nerves, questions and presenting online (Professional Skills).
return [
    'shortname' => 'PRS201',
    'fullname' => 'Presenting with Confidence',
    'category' => 'skills',
    'mandatory' => false,
    'minutes' => 40,
    'cover' => 'img/photo/pm.jpg',
    'summary' => 'Confident presenters are not born. They prepare in a way that makes confidence easier. In 40 minutes you will learn how to plan a clear message, structure it simply, use slides well, manage nerves, handle questions and present online.',
    'outcomes' => [
        'Plan a presentation around your audience and one clear message',
        'Structure a talk with one big idea and three supporting points',
        'Design slides that support you instead of replacing you',
        'Practise effectively and manage nerves',
        'Handle questions calmly and present well online',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why presenting matters, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Presenting with Confidence', 'Confidence comes from preparation',
                        'Whether it is a five-minute team update or a pitch to the board, the same simple steps help you stay clear and calm.') .
                    sb_stats([
                        ['40 min', 'to complete this course, at your own pace'],
                        ['1 + 3', 'one big idea, three supporting points: the structure you will learn'],
                        ['7 out of 10', 'to pass the final check, with as many tries as you need'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'How to start from your audience, not your slides',
                        'A simple structure that works for almost any talk',
                        'How to make slides that help your audience follow',
                        'How to practise, and what to do with nerves',
                        'How to handle questions and present online',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons with practical examples.'],
                        ['Practise', 'Vote in a poll, share a tip and plan a real presentation.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought and download your certificate.'],
                    ]) .
                    sb_note('tip', 'Have a real talk in mind', sb_p('Think of a presentation you have coming up, or one you gave recently. Apply each lesson to it as you go.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: where do you start?', 'package' => sb_h5p('multiple-choice-2-6', 'Where do you start', [
                    'question' => '<p>Priya has been asked to present the new expenses process to the sales team next week. What should she do first?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Think about what the sales team cares about and the one thing they must remember.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. Starting with the audience and the message makes every later step easier.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Open the slide software and start making slides.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Slides come later. Without a clear message, slides tend to fill up with everything.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Write out every word she plans to say, to learn by heart.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>A full script often sounds stiff. Know your points well instead.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Plan your message',
            'summary' => 'Your audience, your message, and a simple structure.',
            'items' => [
                ['type' => 'page', 'name' => 'Know your audience and message', 'content' =>
                    sb_banner('Lesson 1', 'It’s about them, not you', 'A presentation works when the audience leaves knowing, feeling or doing something new.') .
                    sb_p('Before you open any slides, answer three questions. Your answers shape everything else: what you include, what you leave out and how you say it.') .
                    sb_steps([
                        ['Who is listening?', 'What do they already know? What do they care about? What might worry them?'],
                        ['What do I want them to do?', 'Approve a budget, follow a new process, feel confident about a change.'],
                        ['What is my one message?', 'If they remember only one sentence, what should it be? Write it down in 15 words or fewer.'],
                    ]) .
                    sb_columns([
                        ['Message for senior leaders', sb_p('“The new system will cut processing time in half, and we need approval by June to start in the next quarter.”')],
                        ['Message for the team', sb_p('“The new system means fewer manual steps for you, and training starts on 1 July.”')],
                    ]) .
                    sb_note('info', 'Same topic, different message', sb_p('The facts don’t change, but what each audience needs to hear does.'))],
                ['type' => 'page', 'name' => 'One big idea, three points', 'content' =>
                    sb_banner('Lesson 1', 'Simple structures are easy to follow', 'Your audience can’t scroll back. Give them a shape they can hold on to.') .
                    sb_p('Almost any talk fits this structure: one big idea, supported by three points. Three is enough to be convincing and few enough to remember.') .
                    sb_steps([
                        ['Open with the big idea', 'Say your main message in the first minute. A short story, question or surprising fact can lead into it.'],
                        ['Point one', 'With one example or piece of evidence.'],
                        ['Point two', 'With one example or piece of evidence.'],
                        ['Point three', 'With one example or piece of evidence.'],
                        ['Close with the big idea and the ask', 'Repeat the message, and say clearly what you want people to do next.'],
                    ]) .
                    sb_quote('Tell them what you’re going to tell them, tell them, then tell them what you told them.', 'A classic piece of speaking advice') .
                    sb_scenario('Omar has 10 minutes to update the leadership team on a project. He has 25 slides of detail, and wants to show all the work the team has done.',
                        'What would you advise? (A good answer: pick one big idea, such as “we are on track, and need one decision from you”, choose three supporting points, and move the detail into a handout or appendix.)')],
            ],
        ],
        [
            'name' => 'Lesson 2 · Slides and delivery',
            'summary' => 'Slides that help, practice that works and handling nerves.',
            'items' => [
                ['type' => 'book', 'name' => 'Slides, practice and nerves', 'chapters' => [
                    ['Slides that support you', sb_banner('Lesson 2', 'You are the presentation', 'Slides are there to help the audience follow, not to be read aloud.') .
                        sb_dodont(
                            ['One idea per slide', 'Large text that can be read from the back of the room', 'Pictures and simple charts', 'A headline that states the point: “Sales grew in every region”'],
                            ['Full paragraphs of text', 'Reading your slides word for word', 'Busy charts with every number', 'Lots of animations and effects']) .
                        sb_note('tip', 'The glance test', sb_p('Your audience should understand each slide in about three seconds. If they need longer, they are reading, not listening.'))],
                    ['Practice that works', sb_p('Practice is what turns notes into a confident talk. You don’t need to learn it by heart: you need to know your points and your opening well.') .
                        sb_steps([
                            ['Say it out loud', 'Reading it in your head is not practice. Speak it, standing up if you will stand.'],
                            ['Time yourself', 'Most talks run longer than planned. Aim to finish a little early.'],
                            ['Learn your opening', 'Know your first 30 seconds really well. It carries you past the nervous start.'],
                            ['Practise with someone', 'Ask a colleague for one thing that worked and one thing to improve.'],
                        ])],
                    ['Handling nerves', sb_p('Nearly everyone feels nervous before presenting, including experienced speakers. Nerves are not a sign you will do badly: they are energy you can use.') .
                        sb_keypoints('Simple ways to stay calm', [
                            'Breathe slowly: in for four, out for six, a few times before you start',
                            'Arrive early, test the equipment and get used to the room',
                            'Find two or three friendly faces and speak to them',
                            'Pause instead of saying “um”. A pause sounds confident',
                            'Remember: the audience wants you to do well',
                        ]) .
                        sb_note('info', 'They can’t see it', sb_p('Most nervousness is invisible to the audience. You feel your heart racing, but they see someone speaking.'))],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: presenter’s checklist', 'package' => sb_h5p('filltheblanks', 'Presenters checklist', [
                    'text' => '<p>Fill in the missing words from the lessons so far.</p>',
                    'questions' => ['<p>Start planning with your *audience*, not your slides.</p><p>Support your one big idea with *three* points.</p><p>Put only one *idea* on each slide.</p><p>Instead of saying “um”, use a short *pause*.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Questions and online',
            'summary' => 'Handling questions calmly, and presenting well on a video call.',
            'items' => [
                ['type' => 'page', 'name' => 'Handling questions', 'content' =>
                    sb_banner('Lesson 3', 'Questions mean they were listening', 'A question is a chance to make your message clearer, not a test.') .
                    sb_steps([
                        ['Listen to the whole question', 'Don’t start answering halfway through.'],
                        ['Repeat or rephrase it', 'So everyone hears it, and you have a moment to think.'],
                        ['Answer briefly', 'Answer the question asked, then stop. Link back to your message if you can.'],
                        ['Check', '“Does that answer your question?”'],
                    ]) .
                    sb_faq([
                        ['What if I don’t know the answer?', 'Say so honestly: “I don’t know, but I will find out and send it to you by Thursday.” Then do it.'],
                        ['What if someone is hostile?', 'Stay calm and polite. Acknowledge the concern, answer the facts, and offer to continue the discussion afterwards.'],
                        ['What if nobody asks anything?', 'Have one question ready yourself: “A question I am often asked is…”'],
                        ['What if one person keeps asking?', 'Thank them, and offer to talk after the session so others have time.'],
                    ]) .
                    sb_note('warn', 'Don’t guess', sb_p('A confident wrong answer does more damage than “I’ll check and come back to you”.'))],
                ['type' => 'page', 'name' => 'Presenting online', 'content' =>
                    sb_banner('Lesson 3', 'The camera is your audience', 'Online, people are one click away from their email. Keep them with you.') .
                    sb_columns([
                        ['Set up', sb_ul(['Camera at eye level', 'Light in front of you, not behind', 'A quiet room and a headset', 'Other apps and notifications closed'])],
                        ['Deliver', sb_ul(['Look at the camera when making key points', 'Speak a little slower than usual', 'Pause for reactions: there is a short delay', 'Ask a question or run a poll every few minutes'])],
                    ]) .
                    sb_steps([
                        ['Test 15 minutes early', 'Join, share your screen and check sound with the organiser.'],
                        ['Share the right window', 'Share one window, not your whole screen, so private messages stay private.'],
                        ['Name a helper', 'Ask a colleague to watch the chat and help anyone who cannot join.'],
                    ]) .
                    sb_note('tip', 'Keep your notes close', sb_p('Put a few notes just below your camera, so you can glance at them without looking away from your audience.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share your tips and plan a real presentation.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what worries you most?', 'intro' => 'Pick the part of presenting you find most difficult. Results are shown after you answer.',
                    'options' => ['Feeling nervous', 'Difficult questions', 'Keeping people interested', 'Presenting on video calls']],
                ['type' => 'forum', 'name' => 'Share your best presenting tip', 'intro' => 'What has helped you present better? One tip, in a few sentences.',
                    'posts' => [['Welcome: your number one tip', 'Share the one habit or trick that has helped you most when presenting. It could be how you prepare, how you calm down, or how you open.']]],
                ['type' => 'assign', 'name' => 'Plan a five-minute talk', 'dueindays' => 21, 'words' => 250,
                    'intro' => 'Use the structure from Lesson 1 to plan a short presentation.',
                    'instructions' => sb_p('Choose a real topic from your work, such as a project update or a new process. Plan a five-minute talk in 250 words or fewer. Leave out confidential details.') .
                        sb_keypoints('Include', ['Who the audience is and what they care about', 'Your one big idea, in 15 words or fewer', 'Your three supporting points, each with one example', 'What you will ask the audience to do at the end'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Know your audience and message', 'One big idea, three points', 'Slides, practice and nerves', 'Handling questions', 'Presenting online'], 'questions' => [
                    ['mc', 'What should you do first when preparing a presentation?',
                        'Decide who the audience is and the one message they should remember', ['Choose a slide template', 'Write a full script to learn by heart', 'Collect every fact you can find on the topic'],
                        'Starting with the audience and message makes it easier to decide what to include and what to leave out.'],
                    ['mc', 'Which structure is recommended in this course?',
                        'One big idea supported by three points', ['Ten points with equal weight', 'All the background first, conclusion at the end', 'As many slides as there are minutes, plus five'],
                        'One idea and three points is clear, convincing and easy for an audience to remember.'],
                    ['tf', 'Slides work best when they contain everything you are going to say, so the audience can read along.', false,
                        'If the audience is reading, they are not listening. Slides should support you with one idea, short text and simple visuals.'],
                    ['mc', 'Someone asks a question you cannot answer. What is the best response?',
                        'Say you don’t know, and promise to find out and reply by a set time', ['Give your best guess confidently', 'Move on quickly to the next slide', 'Say the question is not relevant'],
                        'Honesty builds trust. A guess that turns out wrong damages your credibility.'],
                    ['mc', 'Which of these helps most with nerves?',
                        'Knowing your first 30 seconds really well', ['Avoiding practice so it feels natural', 'Speaking faster to finish sooner', 'Looking only at your slides'],
                        'A well-practised opening carries you through the most nervous moment, the start.'],
                    ['tf', 'When presenting online, looking at the camera for key points helps you connect with your audience.', true,
                        'Looking at the camera is the online version of eye contact.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Prepare, simplify, practise', 'Three steps to presenting with confidence.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Start with the audience</strong> and one message they should remember.',
                        '<strong>Keep it simple:</strong> one big idea, three points, a clear ask.',
                        '<strong>Let slides support you:</strong> one idea per slide, few words.',
                        '<strong>Practise out loud,</strong> know your opening, and use pauses.',
                        '<strong>Welcome questions,</strong> and be honest when you don’t know.',
                    ])) .
                    sb_note('tip', 'Your next step', sb_p('Volunteer to give the next short update in your team meeting. Use the structure from this course.')) .
                    sb_p('Thank you for completing Presenting with Confidence. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident do you feel about your next presentation?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
