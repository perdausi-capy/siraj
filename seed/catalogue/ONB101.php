<?php
// Welcome to the Company: a new starter's first day (Onboarding). Who we are, our values, how work gets done, tools and help.
return [
    'shortname' => 'ONB101',
    'fullname' => 'Welcome to the Company',
    'category' => 'onboarding',
    'mandatory' => true,
    'minutes' => 30,
    'cover' => 'img/photo/pm.jpg',
    'summary' => 'Your first day is full of new names, new systems and new places. In 30 minutes this course gives you the essentials: who we are, what we value, how work gets done, the tools you will use and who to ask when you are stuck.',
    'outcomes' => [
        'Describe what the company does and how it is organised',
        'Explain our four values and what they look like day to day',
        'Find the tools you need and know who to ask for help',
        'Complete your first-week checklist with confidence',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'A warm welcome, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Welcome to the Company', 'We are glad you are here',
                        'Nobody expects you to know everything on day one. This course gives you a map, so the next few weeks feel less like guesswork.') .
                    sb_stats([
                        ['Day 1', 'is about settling in, meeting people and getting set up, not delivering work'],
                        ['5 days', 'to work through your first-week checklist with your manager'],
                        ['30 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'Who we are, where we work and who our clients are',
                        'The four values that guide how we behave',
                        'How work flows from a client request to a finished result',
                        'The tools you will use every day, and who to ask for help',
                        'What to get done in your first week',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons with real examples from colleagues who started recently.'],
                        ['Practise', 'Answer a quick poll, introduce yourself to other new starters and plan your first week.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought. Your certificate appears once every step is done.'],
                    ]) .
                    sb_note('info', 'Your buddy', sb_p('Every new starter has a buddy: a colleague from another team who has been here a while. Your manager will introduce you on your first day. Your buddy is the person to ask the questions you think are too small to ask anyone else.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: stuck on day one', 'package' => sb_h5p('multiple-choice-2-6', 'Stuck on day one', [
                    'question' => '<p>It is your first morning. Your laptop will not connect to the office Wi-Fi and your manager is in a client meeting until lunch. What is the best thing to do?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Contact the IT service desk, and mention it to your buddy.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. IT is there for exactly this, and your buddy can help you find your way while you wait.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Wait quietly until your manager is free after lunch.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>That wastes half a day. You do not need your manager to fix a technical problem.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Ask a colleague if you can sign in with their account for now.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Never share accounts, even to be helpful. Every sign-in must be your own.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Who we are',
            'summary' => 'What the company does, our values and how work gets done.',
            'items' => [
                ['type' => 'book', 'name' => 'Who we are', 'chapters' => [
                    ['The company', sb_banner('Lesson 1', 'One team, many offices', 'We help organisations solve problems and run better. Here is how we are set up to do it.') .
                        sb_p('We are a professional services company of around 1,500 people. We advise clients, manage projects for them and provide specialist teams for as long as they need them.',
                            'Our largest offices are in the Gulf, with teams in Dubai, Abu Dhabi, Riyadh and Doha. We also have offices in London and Singapore, and colleagues who work with clients across Europe, Africa and Asia. On any one project you may work with people in three or four countries.') .
                        sb_columns([
                            ['Our clients', sb_p('Government bodies, large private companies and growing family businesses. Many have worked with us for over ten years.')],
                            ['Our services', sb_p('Advisory, project delivery and specialist staffing. Most projects combine at least two of these.')],
                            ['Our support teams', sb_p('Finance, People, IT, Legal and Facilities. They keep the business running so client teams can focus on clients.')],
                        ]) .
                        sb_note('tip', 'Learn the org chart early', sb_p('The org chart on the intranet shows every team and who leads it. Spend ten minutes on it this week. It will make meetings much easier to follow.'))],
                    ['Our values', sb_p('Our four values describe how we treat clients and each other. They are used in hiring, in performance reviews and in everyday decisions.') .
                        sb_keypoints('Our values', [
                            '<strong>Integrity:</strong> we tell the truth, keep our promises and do the right thing when nobody is watching',
                            '<strong>Respect:</strong> we treat everyone fairly and listen to views that differ from our own',
                            '<strong>Accountability:</strong> we own our work, including our mistakes',
                            '<strong>Care:</strong> we look after our clients, our colleagues and ourselves',
                        ]) .
                        sb_scenario('Priya joined the Riyadh office two weeks ago. In a client meeting, the client asks whether a report will be ready by Thursday. Priya knows it will not be ready until the following Monday, but her project lead has not said anything.',
                            'Which value applies, and what should Priya do? (Answer: integrity and accountability. After the meeting she should raise it with her project lead straight away, so the client hears an honest date from the team.)') .
                        sb_quote('Alone we can do so little; together we can do so much.', 'Helen Keller')],
                    ['How work gets done', sb_p('Most of our work follows the same pattern, whatever the client or the country.') .
                        sb_steps([
                            ['A client asks for help', 'A partner or account lead agrees the scope, cost and timeline with the client.'],
                            ['A team is formed', 'People are chosen for their skills and availability. You may join several teams at once.'],
                            ['The work is planned', 'The project lead sets tasks, owners and dates in the project tool.'],
                            ['The work is delivered and checked', 'Every client deliverable is reviewed by a second person before it is sent.'],
                            ['We learn from it', 'At the end, the team meets to agree what went well and what to change next time.'],
                        ]) .
                        sb_note('warn', 'Nothing goes to a client unchecked', sb_p('However small the change, a second person reviews anything that goes to a client. If you are asked to send something that has not been reviewed, check with your project lead first.'))],
                ]],
            ],
        ],
        [
            'name' => 'Lesson 2 · Tools and help',
            'summary' => 'The systems you will use every day, and who to ask when you need help.',
            'items' => [
                ['type' => 'page', 'name' => 'Your everyday tools', 'content' =>
                    sb_banner('Lesson 2', 'Set up once, use every day', 'A few systems do most of the work. Get them working in your first two days.') .
                    sb_columns([
                        ['Email and calendar', sb_p('For clients and formal messages. Keep your calendar up to date so colleagues in other time zones can find a slot.')],
                        ['Chat and video calls', sb_p('For quick questions and team conversations. Join your team channel and the new starters channel on day one.')],
                        ['Intranet', sb_p('Policies, the org chart, office information and company news. If you are looking for a form, start here.')],
                    ]) .
                    sb_columns([
                        ['HR system', sb_p('Your personal details, leave requests, payslips and performance goals.')],
                        ['Time and expenses', sb_p('Record your time against projects every Friday. Submit expenses within 30 days, with receipts.')],
                        ['Learning platform', sb_p('This platform. Your mandatory courses are already assigned to you.')],
                    ]) .
                    sb_dodont(
                        ['Set up multi-factor sign-in on day one', 'Save work files in the approved shared drives', 'Set your working hours and time zone in your calendar'],
                        ['Use personal email or apps for company work', 'Share your password, even with IT', 'Install software that IT has not approved']) .
                    sb_note('tip', 'Fill in your profile', sb_p('Add a photo, your role and your office to your profile. Colleagues in other countries often meet you on a video call before they meet you in person.'))],
                ['type' => 'page', 'name' => 'Who to ask', 'content' =>
                    sb_banner('Help is close by', 'Every question has an owner', 'Knowing who to ask saves you hours. Here is where to go for the most common questions.') .
                    sb_columns([
                        ['Your manager', sb_p('Your goals, your priorities, your workload and anything about your role.')],
                        ['Your buddy', sb_p('How things really work here, where things are and the questions you feel awkward asking.')],
                        ['IT service desk', sb_p('Laptop, phone, access, passwords and anything that will not connect.')],
                    ]) .
                    sb_columns([
                        ['People team (HR)', sb_p('Contracts, pay, leave, visas and benefits.')],
                        ['Facilities', sb_p('Building access cards, desks, parking and meeting rooms.')],
                        ['Compliance', sb_p('Gifts, conflicts of interest and anything that does not feel right.')],
                    ]) .
                    '<h3>Questions new starters ask</h3>' .
                    sb_faq([
                        ['I have asked my manager three questions today. Is that too many?', 'No. Managers expect lots of questions in the first month. If you want to save them up, keep a list and go through it together at the end of the day.'],
                        ['My access card does not open the floor I work on.', 'Contact Facilities. Access is set up by location, and it sometimes takes a day to update.'],
                        ['When will I be paid?', 'Pay dates are in the HR system. If you started mid-month, your first payslip covers the days you worked.'],
                        ['Who do I tell if I am ill?', 'Tell your manager before your start time, then record it in the HR system when you are back.'],
                    ])],
                ['type' => 'glossary', 'name' => 'Words you will hear', 'intro' => 'Everyday terms used around the company, in plain language.', 'entries' => [
                    ['Account lead', '<p>The person responsible for our relationship with a client.</p>'],
                    ['Deliverable', '<p>Something we have agreed to give a client, such as a report, a plan or a finished system.</p>'],
                    ['Scope', '<p>What a project will and will not include. Work outside the scope needs to be agreed first.</p>'],
                    ['One-to-one', '<p>A regular private meeting between you and your manager, usually every week or two.</p>'],
                    ['Timesheet', '<p>Your weekly record of the hours you spent on each project or task.</p>'],
                    ['Buddy', '<p>An experienced colleague who helps a new starter settle in during their first months.</p>'],
                ]],
            ],
        ],
        [
            'name' => 'Lesson 3 · Your first week',
            'summary' => 'What to get done in your first five days.',
            'items' => [
                ['type' => 'page', 'name' => 'Your first-week checklist', 'content' =>
                    sb_banner('Lesson 3', 'Five days, one checklist', 'By Friday you should be set up, connected and clear on what comes next.') .
                    sb_steps([
                        ['Day 1: get set up', 'Collect your laptop and access card, sign in, set up multi-factor sign-in and meet your manager and buddy.'],
                        ['Day 2: complete your details', 'Check your personal and bank details in the HR system and read the key policies on the intranet.'],
                        ['Day 3: meet your team', 'Join your team meeting, set up short introductions with the people you will work with most and explore the org chart.'],
                        ['Day 4: start learning', 'Begin your mandatory courses, including Code of Conduct &amp; Ethics and Cyber Security Basics.'],
                        ['Day 5: review the week', 'Meet your manager to go through the checklist and agree your goals for the first 30 days.'],
                    ]) .
                    sb_scenario('Omar started in the Dubai office on Monday. By Thursday he still has not been added to his team’s chat channel and has missed two updates. He does not want to bother anyone.',
                        'What should Omar do? (Answer: ask his manager or buddy to add him today. A missing invite is a small fix, and nobody will think less of him for asking.)') .
                    sb_quote('There is no such thing as a silly question in your first month.', 'A common saying among managers') .
                    sb_note('summary', 'Before Friday', sb_ul([
                        'You can sign in to every system you need',
                        'You have met your manager, your buddy and your team',
                        'You have started your mandatory courses',
                        'You have a meeting booked to agree your 30-day goals',
                    ]))],
                ['type' => 'h5p', 'name' => 'Practice: finish the sentences', 'package' => sb_h5p('filltheblanks', 'Finish the sentences', [
                    'text' => '<p>Fill in the missing words. Each one comes from this course.</p>',
                    'questions' => ['<p>Problems with your laptop or passwords go to the IT service *desk*.</p><p>Your *buddy* helps you learn how things really work here.</p><p>Every client deliverable is *reviewed* by a second person before it is sent.</p><p>Leave requests and payslips are in the *HR* system.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Say hello, share how your first day went and plan your first week.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what are you most looking forward to?', 'intro' => 'Pick one. Results are shown after you answer.',
                    'options' => ['Meeting my team', 'Working with clients', 'Learning new skills', 'Working with colleagues in other countries']],
                ['type' => 'forum', 'name' => 'Introduce yourself', 'intro' => 'Say hello to other new starters. Share your name, role, office and one thing people might not guess about you.',
                    'posts' => [['Welcome, new starters', 'Hello and welcome. Tell us your name, your role and which office you are in, plus one thing you enjoy outside work. Then reply to at least one other new starter.']]],
                ['type' => 'assign', 'name' => 'My first-week plan', 'dueindays' => 7, 'words' => 250,
                    'intro' => 'Plan your first week so you can talk it through with your manager.',
                    'instructions' => sb_p('Using the first-week checklist, write a short plan for your first five days in 250 words or fewer. Share it with your manager at your end-of-week meeting.') .
                        sb_keypoints('Include', ['The three people you most want to meet, and why', 'Any systems or access you are still waiting for', 'The mandatory courses you will start this week', 'Two questions you want to ask your manager'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Welcome check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Who we are', 'Your everyday tools', 'Who to ask', 'Your first-week checklist'], 'questions' => [
                    ['mc', 'Which of these is one of our four values?',
                        'Accountability', ['Speed', 'Hierarchy', 'Competition'],
                        'Our values are integrity, respect, accountability and care.'],
                    ['mc', 'Your access card does not open the floor you work on. Who should you contact?',
                        'Facilities', ['The IT service desk', 'The Compliance team', 'Your client'],
                        'Facilities look after building access, desks, parking and meeting rooms.'],
                    ['tf', 'A deliverable can go to a client without review if it is only a small change.', false,
                        'Everything that goes to a client is reviewed by a second person first, however small the change.'],
                    ['mc', 'A colleague offers to let you use their account until yours is working. What should you do?',
                        'Politely decline and contact the IT service desk', ['Accept, as long as it is only for one day', 'Accept, but do not tell anyone', 'Accept and change their password afterwards'],
                        'Never share accounts. Every sign-in must be your own, and IT can get you set up quickly.'],
                    ['tf', 'In your first month, managers expect you to ask a lot of questions.', true,
                        'Asking questions early is how you learn quickly. Keep a list if you want to save them up.'],
                    ['mc', 'What should you have done by the end of your first week?',
                        'Booked a meeting with your manager to agree your 30-day goals', ['Delivered your first client project', 'Finished every mandatory course', 'Met every person in your office'],
                        'Week one is about getting set up and connected. Agreeing your 30-day goals is the natural next step.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Ask, connect, settle in', 'Three things that make the first week easier.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Ask</strong> early and often. Your manager and buddy expect it.',
                        '<strong>Connect</strong> with your team and the people you will work with most.',
                        '<strong>Settle in</strong> by getting your tools working and following the first-week checklist.',
                    ])) .
                    sb_note('info', 'What comes next', sb_p('Your next course is <strong>Your First 30 Days</strong>. It helps you set goals with your manager and prepare for your 30-day check-in.')) .
                    sb_p('Thank you for completing Welcome to the Company. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the welcome for future new starters.', 'after' => ['Welcome check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your first week?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How clear are you now on who to ask for help?', ['Very clear', 'Clear', 'A little unsure', 'Not clear']],
                        ['text', 'What do you wish someone had told you on your first day?'],
                    ]],
            ],
        ],
    ],
];
