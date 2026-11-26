<?php
// Your First 30 Days: goals, relationships, learning the business, feedback and the 30-day check-in (Onboarding).
return [
    'shortname' => 'ONB102',
    'fullname' => 'Your First 30 Days',
    'category' => 'onboarding',
    'mandatory' => true,
    'minutes' => 35,
    'cover' => 'img/photo/abc.jpg',
    'summary' => 'Your first month sets the tone for everything that follows. In 35 minutes you will learn how to agree clear goals with your manager, build the relationships that matter, learn how the business works, ask for useful feedback and get the most from your 30-day check-in.',
    'outcomes' => [
        'Agree three to five clear goals for your first 30 days with your manager',
        'Build working relationships with your team, stakeholders and colleagues in other offices',
        'Learn how the business makes money and where your role fits',
        'Ask for feedback that is specific and useful',
        'Prepare for and lead your 30-day check-in',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What the first month is for, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Your First 30 Days', 'Make your first month count',
                        'The first 30 days are not about proving yourself. They are about learning fast, building trust and agreeing what good looks like.') .
                    sb_stats([
                        ['3 to 5', 'clear goals to agree with your manager in your first week'],
                        ['30 days', 'until your first formal check-in with your manager'],
                        ['35 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'How to turn a broad role into clear, realistic goals',
                        'Who to get to know first, and how',
                        'How to learn the business quickly without feeling overwhelmed',
                        'How to ask for feedback, and what to do with it',
                        'How to prepare for your 30-day check-in',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons, each built around real situations new starters meet.'],
                        ['Practise', 'Share a tip with other new starters and draft your own 30-day plan.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought. Your certificate appears once every step is done.'],
                    ]) .
                    sb_note('tip', 'Do it alongside your work', sb_p('This course works best over your first two weeks. Read a lesson, try it at work, then come back for the next one.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: a good first goal', 'package' => sb_h5p('multiple-choice-2-6', 'A good first goal', [
                    'question' => '<p>Which of these is the most useful goal for a new starter’s first 30 days?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Meet the eight people I will work with most and understand what each of them needs from me, by day 20.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. It is specific, realistic and has a date, so you will know when it is done.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Learn everything about the company.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Too broad. You cannot tell when it is finished, so it will feel like you are always behind.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Be the highest performer in the team by the end of the month.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Not realistic in month one, and it sets you against your colleagues rather than alongside them.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Setting goals',
            'summary' => 'Agreeing clear, realistic goals with your manager.',
            'items' => [
                ['type' => 'page', 'name' => 'Setting goals with your manager', 'content' =>
                    sb_banner('Lesson 1', 'Agree what good looks like', 'Clear goals stop you guessing. They also give you and your manager something concrete to review.') .
                    sb_p('In your first week, book an hour with your manager to agree your goals for the first 30 days. Come with a draft. It shows initiative and makes the conversation faster.',
                        'Good goals for a new starter are usually about learning, relationships and one or two early pieces of real work. They are not about hitting full targets yet.') .
                    '<h3>Make each goal SMART</h3>' .
                    sb_columns([
                        ['Specific and Measurable', sb_p('Say exactly what will be done, and how you will know. “Shadow three client meetings” is better than “learn about clients”.')],
                        ['Achievable and Relevant', sb_p('Stretching but possible in a month, and linked to what your team actually needs.')],
                        ['Time-bound', sb_p('Give each goal a date. “By day 20” is clearer than “this month”.')],
                    ]) .
                    '<h3>Running the goal-setting meeting</h3>' .
                    sb_steps([
                        ['Ask what success looks like', '“At the end of my first 30 days, what would make you say it has gone well?”'],
                        ['Share your draft goals', 'Three to five goals is plenty. Ask your manager what to add, change or drop.'],
                        ['Agree support', 'Who should you meet? What should you read? What access do you still need?'],
                        ['Write them down', 'Record the agreed goals in the HR system and send your manager a short summary the same day.'],
                    ]) .
                    sb_scenario('James joined the London office as a project analyst. His manager says, “Just get settled in, no pressure.” James likes the relaxed start, but he is not sure what he should be working towards.',
                        'What should James do? (Answer: thank his manager, then suggest three simple goals for the month and ask if they look right. Even a relaxed start goes better with a clear direction.)') .
                    sb_note('warn', 'Goals that are too big', sb_p('A common mistake is to promise too much in month one to make a good impression. Missing an over-ambitious goal looks worse than meeting a sensible one.'))],
            ],
        ],
        [
            'name' => 'Lesson 2 · People and the business',
            'summary' => 'Building relationships, learning how the business works and asking for feedback.',
            'items' => [
                ['type' => 'book', 'name' => 'Relationships and the business', 'chapters' => [
                    ['Building relationships', sb_banner('Lesson 2', 'Trust is built in small moments', 'The people you meet in your first month will shape how quickly you can get things done.') .
                        sb_p('Start with the people you will work with every day, then widen the circle. Ask your manager for a list of five to ten names, and book 20-minute introductions in your first two weeks.') .
                        sb_keypoints('Who to get to know first', [
                            'Your immediate team and your buddy',
                            'The people whose work depends on yours, and whose work you depend on',
                            'Key contacts in support teams, such as Finance and IT',
                            'Colleagues in other offices who work on the same clients',
                        ]) .
                        sb_dodont(
                            ['Ask people what they do and what they need from you', 'Follow up with a short thank-you message', 'Be mindful of time zones and working weeks when booking meetings', 'Join team lunches and informal events'],
                            ['Only reach out when you need something', 'Book long meetings with busy senior people without a clear reason', 'Assume everyone works the same days and hours as you', 'Spend every lunch at your desk']) .
                        sb_note('info', 'Working across offices', sb_p('Working weeks differ across our offices. The UAE works Monday to Friday, while Saudi Arabia and Qatar usually work Sunday to Thursday. Hours are often shorter during Ramadan, and London and Singapore are several hours apart. Check working hours on someone’s calendar before booking a meeting.'))],
                    ['Learning the business', sb_p('You do not need to understand everything at once. Aim to understand how the company makes money, who your team’s clients are, and how your work contributes.') .
                        sb_steps([
                            ['Read the basics', 'The company strategy and latest results on the intranet, plus your team’s plan for the year.'],
                            ['Follow a project', 'Ask to sit in on a client meeting or a project review, and notice how decisions are made.'],
                            ['Learn the numbers', 'Ask your manager which two or three figures your team watches most, and why.'],
                            ['Keep a learning log', 'Note new terms, names and questions every day. Review it once a week.'],
                        ]) .
                        sb_quote('Tell me and I forget, teach me and I may remember, involve me and I learn.', 'A common saying in learning and development') .
                        sb_note('tip', 'Ask “why”, politely', sb_p('New starters see things that long-serving colleagues no longer notice. If a process seems odd, ask why it works that way. Ask to understand first, before suggesting a change.'))],
                    ['Asking for feedback', sb_p('Feedback is one of the fastest ways to learn. Do not wait for your manager to offer it. Ask for it, and make it easy to give.') .
                        sb_columns([
                            ['Ask a specific question', sb_p('“Was my summary at the end of the client call clear enough?” gets a better answer than “Any feedback?”')],
                            ['Ask soon after', sb_p('Ask on the same day, while the details are fresh for both of you.')],
                            ['Say thank you', sb_p('Even when it is hard to hear. Then show you have used it next time.')],
                        ]) .
                        sb_scenario('Fatima, a new consultant in Abu Dhabi, presents her first analysis to the team. Her manager says, “Good start, but the main point got lost.” Fatima feels disappointed and wants to move on quickly.',
                            'What could Fatima do? (Answer: thank her manager, then ask one follow-up question, such as “Where did it get lost, and how would you have opened it?” Then apply it in her next presentation.)')],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: make it SMART', 'package' => sb_h5p('filltheblanks', 'Make it SMART', [
                    'text' => '<p>Fill in the missing words from this course.</p>',
                    'questions' => ['<p>A good goal is Specific, *Measurable*, Achievable, Relevant and Time-bound.</p><p>Aim for three to *five* goals in your first 30 days.</p><p>When you ask for feedback, ask a *specific* question.</p><p>Write down new terms and questions in a learning *log*.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · The 30-day check-in',
            'summary' => 'Preparing for, and getting the most from, your first formal conversation with your manager.',
            'items' => [
                ['type' => 'page', 'name' => 'Your 30-day check-in', 'content' =>
                    sb_banner('Lesson 3', 'Look back, then look ahead', 'The check-in is a two-way conversation about how the month went and what comes next.') .
                    sb_p('Around day 30, your manager will book a check-in with you. It usually takes 45 minutes to an hour. It is not a test. It is a chance to review your goals, raise anything that is not working and agree the next 60 days.') .
                    '<h3>How to prepare</h3>' .
                    sb_steps([
                        ['Review your goals', 'For each goal, note what you did, what is left and what got in the way.'],
                        ['Collect examples', 'Two or three specific pieces of work you are proud of, and one you would do differently.'],
                        ['List what you need', 'Training, access, introductions or clearer guidance on priorities.'],
                        ['Draft your next goals', 'Suggest goals for days 31 to 90. Your manager will expect them to be more about delivery.'],
                    ]) .
                    sb_stats([
                        ['45 to 60 min', 'is a typical length for the check-in'],
                        ['1 week', 'before the meeting is a good time to start preparing'],
                        ['24 hours', 'after the meeting: send a short summary of what you agreed'],
                    ]) .
                    '<h3>Questions people ask</h3>' .
                    sb_faq([
                        ['What if I have not met all my goals?', 'That is normal. Be honest about what happened and what you learned. Managers value a clear explanation far more than excuses.'],
                        ['Can I raise concerns about my role?', 'Yes. The check-in is the right place. Be specific, and suggest what would help.'],
                        ['My manager has not booked the check-in. Should I?', 'Yes. Send a short invitation and say you would like to review your first month together.'],
                        ['Is the check-in part of my probation?', 'It is one input. Your probation review comes later and looks at your first months as a whole.'],
                    ]) .
                    sb_note('summary', 'Bring to your check-in', sb_ul([
                        'Your goals, with progress against each one',
                        'Two or three examples of your work',
                        'What you need to do your best work',
                        'Draft goals for the next 60 days',
                    ]))],
                ['type' => 'glossary', 'name' => 'Terms for your first month', 'intro' => 'Plain-language definitions of words you will hear in goal-setting and reviews.', 'entries' => [
                    ['SMART goal', '<p>A goal that is Specific, Measurable, Achievable, Relevant and Time-bound.</p>'],
                    ['Stakeholder', '<p>Anyone who is affected by your work or has an interest in it, inside or outside the company.</p>'],
                    ['Check-in', '<p>A planned conversation with your manager to review progress and agree next steps.</p>'],
                    ['Probation', '<p>The first months in a new role, when you and the company confirm the role is a good fit.</p>'],
                    ['KPI', '<p>Key performance indicator: one of the few numbers a team uses to track whether it is on course.</p>'],
                ]],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share what has worked for you and draft your own 30-day plan.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what is hardest in month one?', 'intro' => 'Pick the one you find hardest so far. Results are shown after you answer.',
                    'options' => ['Remembering names and roles', 'Knowing what to prioritise', 'Understanding how the business works', 'Asking for feedback']],
                ['type' => 'forum', 'name' => 'First-month tips', 'intro' => 'Share one thing that helped you settle in, or one thing you wish you had done sooner.',
                    'posts' => [['What helped you settle in?', 'Share one practical tip from your first weeks: a person to meet, a habit that helped or a question worth asking. Reply to a colleague whose tip you will try.']]],
                ['type' => 'assign', 'name' => 'My 30-day plan', 'dueindays' => 14, 'words' => 300,
                    'intro' => 'Draft your first-month goals to take to your manager.',
                    'instructions' => sb_p('Write your plan for your first 30 days in 300 words or fewer. Use it as your draft in your goal-setting meeting.') .
                        sb_keypoints('Include', ['Three to five SMART goals, each with a date', 'The five to ten people you plan to meet, and why', 'Two things you want to learn about the business', 'How and when you will ask for feedback'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => '30-day check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Setting goals with your manager', 'Relationships and the business', 'Your 30-day check-in'], 'questions' => [
                    ['mc', 'Which of these is a SMART goal?',
                        'Shadow three client meetings and write a one-page summary of each by day 20', ['Learn as much as possible about our clients', 'Become an expert in the project tool', 'Do my best in every meeting'],
                        'It is specific, measurable, realistic, relevant to the role and has a date.'],
                    ['mc', 'How many goals should a new starter usually agree for the first 30 days?',
                        'Three to five', ['One', 'Ten or more', 'None until probation ends'],
                        'Three to five goals give direction without overloading your first month.'],
                    ['tf', 'You should wait for your manager to offer feedback rather than asking for it.', false,
                        'Asking for feedback, with a specific question, is one of the fastest ways to learn.'],
                    ['mc', 'You want to book a meeting with a colleague in another office. What should you check first?',
                        'Their working hours and time zone on their calendar', ['Their job title', 'How long they have worked here', 'Nothing: book any free slot'],
                        'Offices work across several time zones and working weeks. Checking first is a simple sign of respect.'],
                    ['mc', 'Your manager gives you feedback that is hard to hear. What is the best response?',
                        'Thank them and ask a follow-up question to understand it', ['Explain why they are wrong', 'Say nothing and move on', 'Ask a colleague whether they agree with the feedback'],
                        'Thanking them and asking a clear follow-up turns a hard moment into something you can use.'],
                    ['tf', 'The 30-day check-in is a two-way conversation, and you can raise concerns about your role.', true,
                        'The check-in is for both of you. Be specific about any concerns and suggest what would help.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'Looking ahead', 'content' =>
                    sb_banner('Wrap-up', 'Goals, people, feedback', 'Three things that make your first month count.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Goals:</strong> agree three to five SMART goals in your first week, and write them down.',
                        '<strong>People:</strong> meet the people you will work with most, across offices and time zones.',
                        '<strong>Feedback:</strong> ask specific questions soon after, say thank you and use what you hear.',
                        '<strong>Check-in:</strong> prepare a week ahead and come with examples and next goals.',
                    ])) .
                    sb_quote('What gets measured gets managed.', 'A common saying among managers') .
                    sb_p('Thank you for completing Your First 30 Days. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course for future new starters.', 'after' => ['30-day check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your first month?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How ready do you feel for your 30-day check-in?', ['Very ready', 'Ready', 'A little unsure', 'Not ready']],
                        ['text', 'What one thing would have made your first month easier?'],
                    ]],
            ],
        ],
    ],
];
