<?php
// Leading a Team: for new and recent team leaders (Leadership & Management). Includes a SCORM kick-off simulation and a live Q&A.
return [
    'shortname' => 'LDR301',
    'fullname' => 'Leading a Team',
    'category' => 'leadership',
    'mandatory' => false,
    'minutes' => 50,
    'cover' => 'img/photo/lead.jpg',
    'summary' => 'Being good at the work is how most people become team leaders. Leading the work is a different job. In 50 minutes you will learn the habits that make the move easier: clear goals, weekly one-to-ones, real delegation, a strong kick-off and your first difficult conversation.',
    'outcomes' => [
        'Explain how your job changes when you move from doing to leading',
        'Set goals your team can understand, measure and own',
        'Run a short, useful one-to-one every week',
        'Delegate work with the right level of freedom',
        'Run a project kick-off and handle your first difficult conversation',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What changes when you lead a team, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Leading a Team', 'Your success is now your team’s success',
                        'Yesterday you were judged on your own work. Today you are judged on what your team delivers. A few simple habits make that shift much easier.') .
                    sb_stats([
                        ['50 min', 'to complete this course, at your own pace'],
                        ['3 lessons', 'each with real examples from everyday team life'],
                        ['30 min', 'a week per person: the time a good one-to-one takes'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'How your role changes when you stop doing and start leading',
                        'How to set goals that are clear enough to act on',
                        'How to run a weekly one-to-one that people look forward to',
                        'How to delegate without losing control or micromanaging',
                        'How to start a project well and handle a difficult conversation',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons, with a short interactive module on running a kick-off.'],
                        ['Practise', 'Vote in a poll, share a lesson from your own team and plan a real one-to-one.'],
                        ['Join', 'Bring your questions to the monthly live Q&A with other leaders.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                    ]) .
                    sb_note('info', 'Who this course is for', sb_p('New team leaders, people about to step up, and experienced leaders who want a refresher. No management background is needed.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: your first week', 'package' => sb_h5p('multiple-choice-2-6', 'Your first week', [
                    'question' => '<p>Omar has just been promoted to lead the team he used to work in. What is the most useful thing he can do in his first week?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Meet each person one to one and ask what helps them do their best work.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. Listening first builds trust and shows him where the real problems are.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Take on the hardest tasks himself to prove he deserves the role.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Tempting, but it keeps him doing, not leading, and leaves the team without direction.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Change the team’s processes straight away to show he is in charge.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Changing things before he understands them often loses the team’s trust.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · From doing to leading',
            'summary' => 'How your job changes, and how to set goals your team can act on.',
            'items' => [
                ['type' => 'book', 'name' => 'Moving from doing to leading', 'chapters' => [
                    ['The shift', sb_banner('Lesson 1', 'Less doing, more enabling', 'Your value is no longer what you produce. It is what your team produces because of you.') .
                        sb_p('Most new leaders were promoted because they were good at the work. The trap is to keep doing it: picking up the hard tickets, rewriting reports, answering every client email yourself.',
                            'It feels productive, but it slows the team down. Decisions wait for you, people stop growing, and you end up working late while your team waits for direction.') .
                        sb_columns([
                            ['When you were doing', sb_ul(['You owned tasks', 'You were judged on your own output', 'Your day was your own work', 'Being the expert was the goal'])],
                            ['Now you are leading', sb_ul(['You own outcomes', 'You are judged on the team’s results', 'Your day is people, priorities and problems', 'Growing other experts is the goal'])],
                        ]) .
                        sb_quote('Before you are a leader, success is all about growing yourself. When you become a leader, success is all about growing others.', 'Jack Welch, business leader')],
                    ['Setting clear goals', sb_p('A team without clear goals stays busy but does not move forward. A clear goal answers three questions: <strong>what</strong> we will achieve, <strong>how we will know</strong>, and <strong>by when</strong>.') .
                        sb_steps([
                            ['Start with the outcome', 'Say what will be different, not what people will do. “Reduce customer wait time”, not “answer more calls”.'],
                            ['Make it measurable', 'Agree one number or sign of success. “Average wait under 2 minutes.”'],
                            ['Set a date', '“By the end of March.” Without a date, a goal is a wish.'],
                            ['Agree it together', 'Ask the team how they would reach it. People commit to goals they helped shape.'],
                            ['Check it weekly', 'Look at progress in your team meeting, every week, in five minutes or less.'],
                        ]) .
                        sb_note('tip', 'Three is plenty', sb_p('If everything is a priority, nothing is. Give your team no more than three goals at a time.'))],
                    ['Your first 90 days', sb_keypoints('Focus on these, in this order', [
                            '<strong>Weeks 1–2:</strong> listen. Meet everyone, learn what works and what gets in the way.',
                            '<strong>Weeks 3–6:</strong> agree two or three clear team goals and start weekly one-to-ones.',
                            '<strong>Weeks 7–12:</strong> delegate real work, and fix one thing the team asked you to fix.',
                        ]) .
                        sb_scenario('Priya used to be the team’s best analyst. Now she leads it, and a junior colleague’s report has a mistake the day before it goes to the director. She could fix it herself in twenty minutes.',
                            'What should she do? (A good answer: point out the mistake, let the colleague fix it, and agree how to check reports earlier next time. Fixing it herself solves today and repeats the problem tomorrow.)')],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: what makes a goal clear', 'package' => sb_h5p('filltheblanks', 'What makes a goal clear', [
                    'text' => '<p>Fill in the missing words from this lesson.</p>',
                    'questions' => ['<p>A clear goal describes the *outcome*, not just the activity.</p><p>Every goal needs a way to *measure* success.</p><p>Without a *date*, a goal is only a wish.</p><p>Give your team no more than *three* goals at a time.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 2 · One-to-ones and delegation',
            'summary' => 'The weekly conversation that holds a team together, and how to hand work over well.',
            'items' => [
                ['type' => 'page', 'name' => 'Running weekly one-to-ones', 'content' =>
                    sb_banner('Lesson 2', 'Thirty minutes that save you hours', 'A short, regular conversation catches problems early and tells people they matter.') .
                    sb_p('A one-to-one is <strong>their</strong> meeting, not yours. It is not a status update: you can get that from a report. It is time for the person to raise what is on their mind, get help and grow.') .
                    '<h3>A simple structure</h3>' .
                    sb_steps([
                        ['Their topics first (15 min)', 'Ask “What would you like to talk about?” and let them lead.'],
                        ['Your topics (10 min)', 'Updates they need, feedback, and anything you have noticed.'],
                        ['Growth (5 min)', 'Once a month, ask about their goals: what they want to learn or try next.'],
                        ['Agree actions', 'Write down who will do what. Check them at the start of next week’s meeting.'],
                    ]) .
                    sb_dodont(
                        ['Keep the same slot every week', 'Put your phone away and close your laptop', 'Ask open questions: “What is getting in your way?”', 'Keep short shared notes'],
                        ['Cancel when you are busy: move it instead', 'Turn it into a status report', 'Do most of the talking', 'Bring up a serious problem for the first time without warning']) .
                    sb_note('tip', 'Good questions to ask', sb_ul([
                        'What is going well this week?',
                        'What is one thing I could do to make your work easier?',
                        'Is there anything you are worried about that we haven’t talked about?',
                    ]))],
                ['type' => 'page', 'name' => 'Delegating well', 'content' =>
                    sb_banner('Lesson 2', 'Hand over the outcome, not just the task', 'Good delegation grows your people and frees you to lead.') .
                    sb_p('Many new leaders avoid delegating because “it’s quicker to do it myself”. That is true once. It is not true the fifth time. Delegation is an investment: you spend time now to save time, and build skills, later.') .
                    '<h3>Five steps to delegate</h3>' .
                    sb_steps([
                        ['Choose the right person', 'Think about skills, workload and who would grow from the task.'],
                        ['Explain why it matters', 'Share the purpose and how it fits the bigger picture.'],
                        ['Agree what “done” looks like', 'The result, the deadline and any limits, such as budget or quality.'],
                        ['Set the level of freedom', '“Check with me before you decide”, “decide and tell me”, or “just do it”.'],
                        ['Agree check-ins, then step back', 'Fix the dates you will review progress, and don’t hover in between.'],
                    ]) .
                    sb_note('warn', 'Delegating is not dumping', sb_p('Handing over only the boring tasks, or work you have not explained, is not delegation. People notice, and trust drops quickly.')) .
                    sb_faq([
                        ['What if they do it differently from me?', 'Different is fine if the result is right. Only step in when the outcome, a deadline or a rule is at risk.'],
                        ['What if they make a mistake?', 'Treat it as learning. Ask what they would do differently and agree how to avoid it next time.'],
                        ['Can I delegate the accountability?', 'No. You can hand over the work and the decisions, but you remain accountable to your manager for the result.'],
                    ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Kick-offs and hard conversations',
            'summary' => 'Starting a project the right way, and handling the conversation every leader dreads.',
            'items' => [
                ['type' => 'page', 'name' => 'Running a team kick-off', 'content' =>
                    sb_banner('Lesson 3', 'Start well, finish well', 'An hour spent aligning at the start saves weeks of confusion later.') .
                    sb_p('A kick-off is the first meeting of a project or a new team goal. Its job is simple: everyone leaves knowing <strong>why</strong> the work matters, <strong>what</strong> success looks like, <strong>who</strong> does what and <strong>how</strong> the team will work together.') .
                    sb_keypoints('Cover these five things', [
                        'The purpose: why this project exists and who it helps',
                        'The goal: what done looks like, how it will be measured and the deadline',
                        'Roles: who owns what, and who makes which decisions',
                        'Ways of working: meetings, channels and how to raise a problem',
                        'Risks: what could go wrong, asked openly of the whole team',
                    ]) .
                    sb_note('info', 'Next: the kick-off module', sb_p('The next activity walks you through a kick-off in three short screens. Use it as a checklist for your own next kick-off.'))],
                ['type' => 'scorm', 'name' => 'Interactive: project kick-off', 'package' => 'scorm-kickoff.zip',
                    'intro' => 'Three short screens on running a kick-off: why it matters, the five things every kick-off covers, and the one-page summary to send the same day. It takes about five minutes.'],
                ['type' => 'page', 'name' => 'Your first difficult conversation', 'content' =>
                    sb_banner('Lesson 3', 'Early and kind beats late and sharp', 'The longer you wait, the harder the conversation becomes, for both of you.') .
                    sb_p('Sooner or later you will need to talk to someone about a problem: work that is late again, a poor attitude in meetings, or behaviour that upsets colleagues. Most leaders wait too long, hoping it will fix itself. It rarely does.') .
                    sb_steps([
                        ['Prepare', 'Write down the facts: what happened, when, and the effect. Leave out guesses about why.'],
                        ['Meet in private, soon', 'Choose a quiet place and do it within a few days, not weeks.'],
                        ['Say it clearly', '“I want to talk about the last three reports, which were each two days late.” Get to the point in the first minute.'],
                        ['Listen', 'Ask “What is happening from your side?” There is often a reason you didn’t know.'],
                        ['Agree the next step', 'One or two clear actions, and a date to check in.'],
                    ]) .
                    sb_scenario('James, a well-liked team member, has been arriving 30 minutes late to the morning shift for two weeks. Others are now covering for him and starting to complain.',
                        'How would you open the conversation? (A good opening: “James, I’ve noticed you’ve arrived around 30 minutes late most days for two weeks, and the team has had to cover. Can we talk about what’s going on?”)') .
                    sb_note('warn', 'Know when to get help', sb_p('If the conversation involves health, harassment, discrimination or a possible disciplinary issue, speak to HR before you meet.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share your experience, plan a real one-to-one and join the live Q&A.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what is hardest for you?', 'intro' => 'Pick the part of leading a team you find hardest right now. Results are shown after you answer.',
                    'options' => ['Letting go of my old job', 'Delegating work', 'Having difficult conversations', 'Finding time for one-to-ones']],
                ['type' => 'forum', 'name' => 'Leaders’ corner: lessons from your team', 'intro' => 'Share one thing that worked, or didn’t, when you started leading. Keep names and personal details out of it.',
                    'posts' => [['Welcome: what do you wish you had known?', 'Tell us one thing you wish someone had told you in your first month as a leader. Your answer could save a new leader a hard lesson.']]],
                ['type' => 'assign', 'name' => 'Plan your next one-to-one', 'dueindays' => 21, 'words' => 250,
                    'intro' => 'Plan a real one-to-one with someone in your team, using the structure from Lesson 2.',
                    'instructions' => sb_p('Choose one person in your team (no need to name them). Write a short plan for your next one-to-one with them, in 250 words or fewer.') .
                        sb_keypoints('Include', ['Two open questions you will ask', 'One piece of feedback or recognition you will give', 'One thing you could delegate to them, and the level of freedom', 'How you will record the actions you agree'])],
                ['type' => 'live', 'name' => 'Monthly leaders’ Q&A', 'indays' => 10,
                    'intro' => 'A one-hour live online session with experienced leaders. Bring a real question about your team, from delegation to difficult conversations. Join from your laptop at the time shown.'],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Moving from doing to leading', 'Running weekly one-to-ones', 'Delegating well', 'Running a team kick-off', 'Your first difficult conversation'], 'questions' => [
                    ['mc', 'A colleague’s report has a mistake the day before it is due. What is the best response from a team leader?',
                        'Point out the mistake, let them fix it and agree how to check earlier next time', ['Fix it yourself quietly so it is right', 'Send it as it is and raise it at the annual review', 'Give the task to someone more experienced from now on'],
                        'Fixing it yourself solves today’s problem but teaches nothing. Coaching the person fixes the problem for good.'],
                    ['mc', 'Which of these is the clearest team goal?',
                        'Reduce average customer wait time to under 2 minutes by the end of March', ['Answer more customer calls', 'Provide excellent customer service', 'Work harder on the phones this quarter'],
                        'A clear goal states the outcome, how it will be measured and the date.'],
                    ['tf', 'A one-to-one should mainly be used to get a status update on each task.', false,
                        'A one-to-one is the team member’s time to raise issues, get help and grow. Status updates belong in reports or team meetings.'],
                    ['mc', 'When you delegate a task, what remains with you?',
                        'Accountability for the result', ['Every decision about how the work is done', 'Nothing: it is fully handed over', 'Doing the difficult parts of the task'],
                        'You hand over the work and agreed decisions, but you stay accountable to your manager for the result.'],
                    ['mc', 'Which of these belongs in a project kick-off?',
                        'Agreeing roles and who makes which decisions', ['Assigning blame for the last project', 'Reviewing everyone’s annual performance', 'Leaving risks until something goes wrong'],
                        'A kick-off sets purpose, goal, roles, ways of working and risks, so everyone starts aligned.'],
                    ['tf', 'It is usually best to wait a few weeks before raising a performance problem, in case it fixes itself.', false,
                        'Problems rarely fix themselves. Raising them early, in private and with facts, is kinder and more effective.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Lead the people, not just the work', 'Five habits to start this week.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Step back</strong> from doing, and judge yourself by your team’s results.',
                        '<strong>Set</strong> no more than three clear goals, with a measure and a date.',
                        '<strong>Meet</strong> each person one to one every week, and let them lead the conversation.',
                        '<strong>Delegate</strong> outcomes with a clear level of freedom, then step back.',
                        '<strong>Speak early</strong> when something is wrong: in private, with facts, and ready to listen.',
                    ])) .
                    sb_note('tip', 'Your next step', sb_p('Book a recurring one-to-one slot with each person in your team before you close this page.')) .
                    sb_p('Thank you for completing Leading a Team. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your work as a leader?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about delegating work?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
