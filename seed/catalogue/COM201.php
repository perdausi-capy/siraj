<?php
// Clear Communication at Work: writing, channels, listening, meetings and working across cultures (Professional Skills).
return [
    'shortname' => 'COM201',
    'fullname' => 'Clear Communication at Work',
    'category' => 'skills',
    'mandatory' => false,
    'minutes' => 35,
    'cover' => 'img/photo/abc.jpg',
    'summary' => 'Unclear messages cost time: extra emails, long meetings and work done twice. In 35 minutes you will learn simple habits for writing clearly, choosing the right channel, listening well, running useful meetings and working with colleagues from many cultures.',
    'outcomes' => [
        'Write emails and messages that put the main point first',
        'Choose the right channel for each message',
        'Listen actively and check your understanding',
        'Run a meeting with a clear agenda and agreed actions',
        'Communicate clearly with colleagues from other cultures and languages',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why clear communication matters, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Clear Communication at Work', 'Say it once, say it clearly',
                        'Most misunderstandings are not about attitude. They come from messages that bury the point, arrive in the wrong place or assume too much.') .
                    sb_stats([
                        ['35 min', 'to complete this course, at your own pace'],
                        ['5 habits', 'that make your messages, meetings and conversations clearer'],
                        ['7 out of 10', 'to pass the final check, with as many tries as you need'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'How to write emails and messages people read and act on',
                        'When to email, message, call or meet',
                        'How to listen so people feel heard',
                        'How to run meetings that end with clear actions',
                        'How to communicate across cultures and languages',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons with before-and-after examples.'],
                        ['Practise', 'Fill in a meeting checklist, vote in a poll and rewrite a real email.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought and download your certificate.'],
                    ]) .
                    sb_note('tip', 'Bring a real example', sb_p('Keep a recent email or message you wrote open while you take this course. You will rewrite it at the end.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: which email gets an answer?', 'package' => sb_h5p('multiple-choice-2-6', 'Which email gets an answer', [
                    'question' => '<p>James needs approval from his manager to book a venue by Thursday. Which email opening is most likely to get a quick answer?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>“Can you approve the venue booking by Thursday? The details are below.”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. The request and the deadline come first, so the reader knows what to do straight away.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>“As you may remember, last year we held the event in a different venue, and there were some issues with parking…”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>The background may be useful, but the request is buried. Busy readers may never reach it.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>“Hope you are well. Just a quick one about the event.”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Friendly, but the reader still doesn’t know what is needed or by when.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Writing that gets read',
            'summary' => 'Putting the point first, and choosing the right channel.',
            'items' => [
                ['type' => 'page', 'name' => 'Bottom line up front', 'content' =>
                    sb_banner('Lesson 1', 'Lead with the point', 'Your reader is busy. Tell them what you need in the first two lines.') .
                    sb_p('“Bottom line up front” means starting with your conclusion or request, then giving the detail. It is the opposite of how we often write: background first, point last.') .
                    sb_columns([
                        ['Before', sb_p('“Following our meeting last week, I have been looking at the supplier options. There are three. Each has pros and cons, which I have listed below. After thinking about it, I would suggest the second one, if you agree…”')],
                        ['After', sb_p('“I recommend Supplier B. Can you approve by Wednesday so we can order this month? A short comparison of all three is below.”')],
                    ]) .
                    '<h3>Four steps to a clear message</h3>' .
                    sb_steps([
                        ['Write a subject that says it all', '“Approval needed by Wed: Supplier B”, not “Suppliers”.'],
                        ['Put the request or answer first', 'One or two sentences: what you need, and by when.'],
                        ['Add only the detail they need', 'Short paragraphs or a few bullet points.'],
                        ['Read it as the reader', 'Could they act on it after reading just the first two lines?'],
                    ]) .
                    sb_quote('I have made this letter longer than usual, only because I have not had the time to make it shorter.', 'Blaise Pascal, mathematician and writer') .
                    sb_note('tip', 'One email, one topic', sb_p('If you have three unrelated questions, send three short emails. Each one can be answered, forwarded and filed on its own.'))],
                ['type' => 'page', 'name' => 'Choosing the right channel', 'content' =>
                    sb_banner('Lesson 1', 'Match the message to the medium', 'The right words in the wrong place still cause problems.') .
                    sb_columns([
                        ['Chat or instant message', sb_p('Quick questions, short updates, simple yes or no. Not for anything long, sensitive or that needs a record.')],
                        ['Email', sb_p('Decisions, requests with detail, anything people need to find again later, messages to people outside the company.')],
                        ['Call or video', sb_p('Anything complex, urgent or emotional. When a chat thread passes five messages, pick up the phone.')],
                        ['Face to face', sb_p('Difficult conversations, feedback, building relationships and big news.')],
                    ]) .
                    sb_dodont(
                        ['Move to a call when a thread goes in circles', 'Confirm decisions from a call in writing', 'Use the team’s agreed channels for each type of work'],
                        ['Give bad news by chat', 'Send urgent requests only by email and wait', 'Copy everyone “just in case”']) .
                    sb_note('warn', 'Think before you reply all', sb_p('Only reply to everyone if everyone needs your answer. Otherwise, reply to the sender.'))],
            ],
        ],
        [
            'name' => 'Lesson 2 · Listening and meetings',
            'summary' => 'Active listening, and meetings that end with clear actions.',
            'items' => [
                ['type' => 'book', 'name' => 'Listening and meetings', 'chapters' => [
                    ['Active listening', sb_banner('Lesson 2', 'Listen to understand', 'Good communication is half listening, and it is the half most of us skip.') .
                        sb_quote('Most people do not listen with the intent to understand; they listen with the intent to reply.', 'Stephen R. Covey, author') .
                        sb_steps([
                            ['Give full attention', 'Put the phone down, look at the speaker, close other windows on a call.'],
                            ['Let them finish', 'Don’t interrupt, even when you think you know the end of the sentence.'],
                            ['Ask open questions', '“What happened next?” “What would help?”'],
                            ['Check what you heard', '“So the main problem is the delivery date, not the price. Is that right?”'],
                        ]) .
                        sb_note('tip', 'Silence is fine', sb_p('A short pause after someone finishes gives them space to add the thing that really matters.'))],
                    ['Meetings with an agenda', sb_p('A meeting without an agenda is a conversation that costs everyone’s time. A good agenda turns it into work.') .
                        sb_keypoints('A good agenda has', [
                            'A clear purpose: what will be decided or produced',
                            'Topics in order, each with a time and an owner',
                            'The right people only: everyone there has a reason to be there',
                            'Any reading sent at least a day before',
                        ]) .
                        sb_faq([
                            ['What if there is no agenda?', 'Ask the organiser politely: “What would you like us to decide in this meeting?” If there is no answer, it may not need to be a meeting.'],
                            ['How long should a meeting be?', 'As short as the purpose allows. Try 25 or 50 minutes instead of 30 or 60, so people have a break between meetings.'],
                            ['Can I decline a meeting?', 'Yes, if you don’t need to be there. Explain briefly and offer to read the notes or send your input.'],
                        ])],
                    ['Actions that happen', sb_p('Many meetings end with everyone agreeing, and nobody doing anything. Before you close, agree the actions out loud.') .
                        sb_steps([
                            ['What', 'A clear action: “Send the revised quote to the client.”'],
                            ['Who', 'One named person, not “the team”.'],
                            ['When', 'A date: “By Tuesday.”'],
                        ]) .
                        sb_scenario('Fatima’s weekly project meeting always runs 20 minutes over, and the same issues come back every week. Nobody is sure who is meant to fix them.',
                            'What two changes would help most? (A good answer: send an agenda with times beforehand, and end every meeting by agreeing what, who and when for each action, then send them round in writing.)')],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: meeting checklist', 'package' => sb_h5p('filltheblanks', 'Meeting checklist', [
                    'text' => '<p>Fill in the missing words from this lesson.</p>',
                    'questions' => ['<p>Every meeting needs an *agenda* sent beforehand.</p><p>Each action needs a what, a *who* and a when.</p><p>To check your understanding, *repeat* back what you heard in your own words.</p><p>When a chat thread goes in circles, move to a *call*.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Across cultures and languages',
            'summary' => 'Being clear with colleagues from many backgrounds.',
            'items' => [
                ['type' => 'page', 'name' => 'Communicating across cultures', 'content' =>
                    sb_banner('Lesson 3', 'Clear for everyone, not just for you', 'In a team from many countries, the clearest message is the simplest one.') .
                    sb_p('Many of us work with colleagues who speak English as a second or third language, and who grew up with different ideas about directness, hierarchy and time. What sounds polite to one person can sound rude, or unclear, to another.') .
                    sb_keypoints('Plain-language habits', [
                        'Use short sentences and common words',
                        'Avoid idioms and slang, like “touch base” or “ballpark figure”',
                        'Write dates clearly: “3 March 2027”, not “3/3”',
                        'Say times with the time zone when people are in different countries',
                        'Put important points in writing after a call',
                    ]) .
                    sb_scenario('Priya asks a new colleague, Ahmed, “Could you possibly have a look at the report when you get a chance?” She expects it today. Ahmed reads it as a low-priority request for next week.',
                        'How could Priya say it more clearly? (A good answer: “Please review the report and send me comments by 4 pm today.” Polite, and clear about what and when.)') .
                    sb_note('info', 'Directness varies', sb_p('Some colleagues will say “no” directly. Others will say “that may be difficult”. Neither is wrong. If you are not sure what someone means, ask a gentle, open question.')) .
                    sb_faq([
                        ['Should I speak more slowly?', 'A little, and pause between points. Don’t raise your voice or simplify so much that it sounds patronising.'],
                        ['How do I check I was understood without seeming rude?', 'Ask the other person to summarise the next steps: “Just so we’re aligned, what will you do first?”'],
                    ])],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share your experience and rewrite a real message.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what slows you down most?', 'intro' => 'Pick the communication problem that costs you the most time. Results are shown after you answer.',
                    'options' => ['Long emails with no clear point', 'Meetings with no agenda or actions', 'Too many chat messages', 'Misunderstandings between teams']],
                ['type' => 'forum', 'name' => 'Lost in translation', 'intro' => 'Share a time a message was misunderstood, and what you learned. Keep names and personal details out of it.',
                    'posts' => [['Welcome: when did a message go wrong?', 'Tell us about a misunderstanding at work: what was said, what was heard, and how it was fixed. Funny stories welcome.']]],
                ['type' => 'assign', 'name' => 'Rewrite an email', 'dueindays' => 21, 'words' => 200,
                    'intro' => 'Put bottom line up front into practice on a real message.',
                    'instructions' => sb_p('Choose a recent email you wrote or received that was unclear. Remove any names or confidential details. Rewrite it using the steps from Lesson 1, in 200 words or fewer.') .
                        sb_keypoints('Include', ['A subject line that says what is needed', 'The request or answer in the first two lines', 'A clear deadline if one is needed', 'One sentence on what you changed and why'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Bottom line up front', 'Choosing the right channel', 'Listening and meetings', 'Communicating across cultures'], 'questions' => [
                    ['mc', 'What does “bottom line up front” mean?',
                        'Start with your main point or request, then give the detail', ['Put the most important point at the end', 'Write the shortest email possible, with no detail', 'Put the deadline in the subject line only'],
                        'Busy readers act on the first lines they read. Lead with the request or conclusion.'],
                    ['mc', 'A chat thread about a complex problem has passed ten messages without agreement. What should you do?',
                        'Suggest a short call, then confirm the decision in writing', ['Keep messaging until someone agrees', 'Add more people to the chat', 'Send a long email to everyone'],
                        'Complex topics are faster to settle by voice. Writing down the decision afterwards gives everyone a record.'],
                    ['tf', 'Active listening includes repeating back what you heard to check your understanding.', true,
                        'Summarising what you heard shows you listened and catches misunderstandings early.'],
                    ['mc', 'Which action from a meeting is most likely to get done?',
                        '“Omar will send the revised quote to the client by Tuesday.”', ['“The team will look into the quote.”', '“We should probably update the quote soon.”', '“Someone to follow up with the client.”'],
                        'A clear action has a what, a named who and a when.'],
                    ['mc', 'Which is the clearest way to give a deadline to an international team?',
                        '“By 3 pm UAE time on Thursday 4 March”', ['“By close of play”', '“Sometime this week”', '“By 4/3”'],
                        'Exact dates and time zones avoid confusion. Idioms and short date formats are read differently in different countries.'],
                    ['tf', 'Idioms such as “touch base” or “ballpark figure” help make messages friendlier for international colleagues.', false,
                        'Idioms are often confusing for people working in a second language. Plain words are clearer for everyone.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Clear, short, checked', 'Five habits for every message and meeting.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Lead with the point:</strong> request and deadline in the first two lines.',
                        '<strong>Pick the right channel:</strong> chat for quick, email for record, a call for complex.',
                        '<strong>Listen to understand,</strong> then check what you heard.',
                        '<strong>Run meetings with an agenda,</strong> and end with what, who and when.',
                        '<strong>Use plain words</strong> so every colleague understands, whatever their first language.',
                    ])) .
                    sb_p('Thank you for completing Clear Communication at Work. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How likely are you to change how you write emails?', ['Very likely', 'Likely', 'Not sure', 'Unlikely']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
