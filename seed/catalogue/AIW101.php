<?php
// Using AI at Work (Digital & Security): practical, vendor-neutral habits for generative AI assistants.
return [
    'shortname' => 'AIW101',
    'fullname' => 'Using AI at Work',
    'category' => 'digital',
    'mandatory' => false,
    'minutes' => 35,
    'cover' => 'img/photo/infosec.jpg',
    'summary' => 'AI assistants can draft, summarise and explain in seconds. They can also be confidently wrong, and anything you paste in may leave the company. In 35 minutes you will learn how to ask well, check what comes back and keep company data safe.',
    'outcomes' => [
        'Explain what AI assistants do well and where they fail',
        'Write clear prompts with context, task, format and examples',
        'Check AI output for errors and made-up facts before you use it',
        'Keep personal and confidential data out of public AI tools',
        'Be open about when and how you used AI in your work',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why AI skills matter, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Using AI at Work', 'A fast assistant that needs a careful boss',
                        'AI can save you real time on everyday writing and thinking tasks. You stay responsible for what it produces and for what you feed it.') .
                    sb_stats([
                        ['3', 'short lessons: asking well, checking output, using AI safely'],
                        ['35 min', 'to complete the course, at your own pace'],
                        ['7 / 10', 'is the pass mark for the final check, and you can try again'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'Which tasks suit an AI assistant, and which do not',
                        'A simple four-part recipe for a good prompt',
                        'How to spot mistakes and invented facts in AI answers',
                        'What you must never paste into a public AI tool',
                        'How to be transparent about AI use with colleagues and customers',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons built around everyday tasks: emails, summaries, reports and research.'],
                        ['Practise', 'Build a prompt, vote in a quick poll, share a tip and improve a weak prompt of your own.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass.'],
                        ['Finish', 'Tell us what you thought and collect your certificate.'],
                    ]) .
                    sb_note('info', 'No special tools needed', sb_p('This course does not teach any one product. The habits work with whichever AI tool the company has approved for you.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: a good job for AI?', 'package' => sb_h5p('multiple-choice-2-6', 'A good job for AI', [
                    'question' => '<p>Which of these tasks is the best fit for an AI assistant, with you checking the result?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Turning your rough meeting notes into a tidy first draft of the follow-up email.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. Drafting from material you provide is a strength, and you can easily check the result.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Deciding which candidate to hire, based on their CVs.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Not a good fit. Decisions about people need human judgement, and CVs contain personal data.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Giving you today’s exact exchange rate for a customer invoice.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Risky. AI tools may give out-of-date or invented figures. Use the official source.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Ask well',
            'summary' => 'What AI assistants are good and bad at, and how to write a prompt that gets a useful answer.',
            'items' => [
                ['type' => 'page', 'name' => 'Strengths and limits', 'content' =>
                    sb_banner('Lesson 1', 'Brilliant at words, unreliable on facts',
                        'An AI assistant predicts likely text. That makes it a strong writer and a weak source of truth.') .
                    sb_p('Generative AI assistants learn patterns from huge amounts of text. When you ask a question, they produce the answer that sounds most likely. Most of the time that is useful. Sometimes it is fluent, confident and wrong.') .
                    sb_columns([
                        ['Good at', sb_ul([
                            'First drafts of emails, reports and job adverts',
                            'Summarising long documents you provide',
                            'Rewording text for a different reader or tone',
                            'Brainstorming ideas, headings and questions',
                            'Explaining a concept in plain language',
                        ])],
                        ['Bad at', sb_ul([
                            'Exact facts, figures, dates and quotes',
                            'Recent events and company-specific knowledge',
                            'Legal, medical or financial judgement',
                            'Decisions about people',
                            'Knowing when it does not know',
                        ])],
                    ]) .
                    sb_note('warn', 'Confidence is not accuracy', sb_p('An AI answer sounds equally sure whether it is right or wrong. It can invent names, statistics, references and even policies. This is often called a <em>hallucination</em>.')) .
                    sb_scenario('Omar asks an AI tool to list the main changes in a new regulation that affects his team. It returns five tidy bullet points, each with a section number.',
                        'Can Omar send this to his manager as it is? (Answer: no. He should check every point and section number against the regulation itself. The tool may have mixed up or invented details.)')],
                ['type' => 'book', 'name' => 'Writing good prompts', 'chapters' => [
                    ['The four parts of a prompt', sb_banner('Prompt recipe', 'Context, task, format, examples',
                            'Most weak answers come from weak questions. Four ingredients fix most of them.') .
                        sb_steps([
                            ['Context', 'Who you are, who the reader is and what the situation is. “I manage a customer support team. Our customers are small business owners.”'],
                            ['Task', 'Exactly what you want done. “Write a reply apologising for a two-day delivery delay.”'],
                            ['Format', 'Length, tone and layout. “Under 120 words, friendly but professional, with one clear next step.”'],
                            ['Examples', 'A sample of what good looks like. “Here is a reply we sent last month that customers liked: …”'],
                        ]) .
                        sb_note('tip', 'Talk to it like a new colleague', sb_p('Imagine briefing a smart new starter who knows nothing about your team. If they would need more information, so does the AI.'))],
                    ['Before and after', sb_p('Here is the same request, written two ways.') .
                        sb_columns([
                            ['Weak prompt', sb_p('<em>“Write an email about the training.”</em>') . sb_p('The tool has to guess who it is for, what training, what tone and how long. You will get something generic.')],
                            ['Strong prompt', sb_p('<em>“I am a team leader. Write a short email to my team of eight reminding them that the safety refresher is on Thursday at 10:00 in Room 2. Keep it under 80 words, warm and clear, and ask them to reply if they cannot attend.”</em>') . sb_p('Context, task and format are all there. The draft will need only small changes.')],
                        ]) .
                        sb_quote('If I had an hour to solve a problem, I would spend most of it defining the problem.', 'A common saying about problem solving')],
                    ['Improve the answer', sb_p('Your first prompt is a starting point, not a final order. Treat it as a conversation.') .
                        sb_keypoints('Ways to steer a response', [
                            'Ask for a shorter, simpler or more formal version',
                            'Point out what is wrong: “The second paragraph repeats the first. Remove it.”',
                            'Ask it to list its assumptions, so you can correct them',
                            'Ask for three options and choose the best one',
                            'Ask what information is missing to do the task well',
                        ]) .
                        sb_faq([
                            ['Do I need special words or tricks?', 'No. Clear, plain instructions work best. The four-part recipe matters more than any trick.'],
                            ['Should I start again or keep refining?', 'Keep refining while the answer is getting closer. Start a fresh conversation if it keeps going in the wrong direction.'],
                            ['Can I reuse a good prompt?', 'Yes. Save prompts that work for repeated tasks, without any confidential details, and share them with your team.'],
                        ])],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: build a prompt', 'package' => sb_h5p('filltheblanks', 'Build a prompt', [
                    'text' => '<p>Fill in the missing words from the prompt recipe in this lesson.</p>',
                    'questions' => ['<p>“I manage a team of six sales staff” gives the AI *context*.</p><p>“Write a reminder about expense deadlines” is the *task*.</p><p>“Under 100 words, as three bullet points” sets the *format*.</p><p>Pasting a past message that worked well gives the AI an *example*.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 2 · Check what comes back',
            'summary' => 'How to review AI output for accuracy, invented facts and tone before you use it.',
            'items' => [
                ['type' => 'page', 'name' => 'Checking AI output', 'content' =>
                    sb_banner('Lesson 2', 'You sign it, you own it',
                        'Whatever you send, publish or decide is your work, however it was drafted.') .
                    sb_p('AI saves time on the first draft. The time you save is partly spent on checking. Skip the check and you risk sending a customer a wrong price, quoting a policy that does not exist or citing a report nobody wrote.') .
                    '<h3>A five-point check</h3>' .
                    sb_steps([
                        ['Facts and figures', 'Check every number, date, name and price against a reliable source, such as the original document or an official website.'],
                        ['Sources and references', 'If the answer cites a report, law or article, find it yourself. AI tools sometimes invent convincing references.'],
                        ['Company knowledge', 'The tool does not know your current policies, prices or customers. Check anything company-specific with the owner.'],
                        ['Fairness and tone', 'Read it as the recipient would. Remove stereotypes, unfair assumptions or a tone that does not fit.'],
                        ['Sense check', 'Does it actually answer the question? Does it make sense as a whole? Would you be happy to put your name to it?'],
                    ]) .
                    sb_dodont(
                        ['Check the original source for any fact you rely on', 'Ask the tool where a claim comes from, then verify it', 'Get an expert to review legal, financial, health or safety content', 'Rewrite in your own voice'],
                        ['Copy and paste output straight to a customer', 'Trust a figure because it looks precise', 'Assume a reference exists because it has a title and a date', 'Let AI make decisions about people']) .
                    sb_quote('Trust, but verify.', 'A Russian proverb, made famous by Ronald Reagan') .
                    sb_scenario('Layla uses an AI tool to summarise a 40-page supplier contract. The summary says the notice period is 30 days.',
                        'What should she do before sharing the summary with her manager? (Answer: find the notice period clause in the contract and check it. Key terms like dates, fees and penalties must always be read in the original.)')],
                ['type' => 'glossary', 'name' => 'AI terms', 'intro' => 'Plain-language definitions of the words used in this course.', 'entries' => [
                    ['Generative AI', '<p>Software that produces new text, images or other content based on patterns learned from large amounts of data.</p>'],
                    ['AI assistant', '<p>A chat-style tool that answers questions and carries out writing and analysis tasks from your instructions.</p>'],
                    ['Prompt', '<p>The instructions and information you give an AI assistant.</p>'],
                    ['Hallucination', '<p>When an AI tool produces something false, such as an invented fact or reference, and presents it as true.</p>'],
                    ['Public AI tool', '<p>An AI tool open to anyone, usually on a personal account, that the company has not approved or contracted for work data.</p>'],
                    ['Approved tool', '<p>An AI tool the company has checked and allowed for work, with rules about what data it may be used with.</p>'],
                ]],
            ],
        ],
        [
            'name' => 'Lesson 3 · Use AI safely and openly',
            'summary' => 'Protecting data, being transparent about AI use and sticking to approved tools.',
            'items' => [
                ['type' => 'page', 'name' => 'What never to paste in', 'content' =>
                    sb_banner('Lesson 3', 'Think before you paste',
                        'Text you type into a public AI tool leaves the company. You may not be able to get it back or delete it.') .
                    sb_p('Many public AI tools store conversations and may use them to improve their service. Even when they do not, the data has left systems the company controls. Treat a public AI tool like a public website: only enter what you would be happy to see published.') .
                    sb_keypoints('Never paste into a public AI tool', [
                        '<strong>Personal data</strong>: names with contact details, ID or passport numbers, health information, salaries, performance reviews, CVs',
                        '<strong>Customer data</strong>: account details, orders, complaints with names, anything covered by a contract',
                        '<strong>Confidential company information</strong>: financial results before release, prices, plans, contracts, legal matters',
                        '<strong>Security details</strong>: passwords, access codes, network information, source code with secrets in it',
                    ]) .
                    sb_note('tip', 'Remove the details, keep the problem', sb_p('You can often get help without the sensitive part. Replace names with “Customer A”, round the figures and strip out anything that identifies a person or deal. Even then, use an approved tool for work tasks.')) .
                    sb_scenario('Sara wants help writing a difficult performance review. She plans to paste last year’s review, with the employee’s name and comments, into a free AI website.',
                        'What should she do instead? (Answer: not paste the review. She can ask an approved tool for a general structure for a balanced review, then write the personal content herself.)')],
                ['type' => 'page', 'name' => 'Being open about AI use', 'content' =>
                    sb_banner('Transparency', 'No hidden helpers',
                        'Being honest about AI use builds trust. Hiding it damages trust when it comes out.') .
                    sb_p('You do not need to label every email that AI helped you tidy up. But people deserve to know when AI played a significant part in work they rely on.') .
                    sb_columns([
                        ['Say so when', sb_ul([
                            'AI drafted a large part of a report, analysis or proposal',
                            'A customer or partner may assume a person wrote or checked something',
                            'A manager or client asks how the work was produced',
                            'Your team or the company requires it',
                        ])],
                        ['How to say it', sb_ul([
                            '“First draft prepared with an AI assistant, reviewed and edited by me.”',
                            '“Summary generated with AI from the attached minutes. Figures checked against the original.”',
                            'Keep it short and factual. No need to apologise.',
                        ])],
                    ]) .
                    sb_note('warn', 'Never pass off AI work as checked when it is not', sb_p('Saying “I have reviewed this” means you have read it carefully and checked the facts. If you have not, do not say it.')) .
                    sb_faq([
                        ['Is using AI a kind of cheating?', 'No. Using an approved tool to work faster is fine. What matters is that the result is accurate, fair and honest about how it was made.'],
                        ['Do I need to mention AI if I only fixed spelling with it?', 'Usually not. Light editing is like using a spell checker. Mention it when AI shaped the content.'],
                    ])],
                ['type' => 'page', 'name' => 'Approved tools', 'content' =>
                    sb_banner('Company policy', 'Use the tools the company has checked',
                        'Approved tools come with agreements about how your data is stored and used. Personal accounts do not.') .
                    sb_p('The company decides which AI tools may be used for work and what data each one may handle. The details are in the company’s AI or acceptable use policy. The general rules are simple.') .
                    sb_steps([
                        ['Use approved tools for work', 'Sign in with your work account, not a personal one.'],
                        ['Match the data to the tool', 'Some approved tools may handle internal data, others only public information. Check before you use them.'],
                        ['Ask before you add', 'Want to try a new AI tool, plug-in or browser extension? Ask IT first. Do not install it and see.'],
                        ['Report problems', 'If you pasted something you should not have, or a tool behaves oddly, tell IT or your manager straight away.'],
                    ]) .
                    sb_note('info', 'Not sure if a tool is approved?', sb_p('Check the IT pages on the intranet or ask the IT service desk. If you cannot confirm it, treat it as not approved.')) .
                    sb_dodont(
                        ['Use your work account in approved tools', 'Read the guidance for each tool', 'Report accidental sharing quickly'],
                        ['Use personal AI accounts for company work', 'Install AI extensions or apps without approval', 'Connect AI tools to company systems yourself'])],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share how you use AI, and improve a prompt from your own work.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: where would AI help you most?', 'intro' => 'Pick the task where an AI assistant would save you the most time. Results are shown after you answer.',
                    'options' => ['Drafting emails and messages', 'Summarising long documents', 'Writing reports and proposals', 'Brainstorming ideas', 'Explaining unfamiliar topics']],
                ['type' => 'forum', 'name' => 'Share a prompt that worked', 'intro' => 'Share a prompt that gave you a useful result, and what made it work. Remove all names, customer details and confidential information first.',
                    'posts' => [['Welcome: what is your best prompt?', 'Post a prompt that saved you time, with the personal and confidential details removed. Say which part of the recipe made the difference: context, task, format or an example.']]],
                ['type' => 'assign', 'name' => 'Improve a weak prompt', 'dueindays' => 21, 'words' => 250,
                    'intro' => 'Rewrite a vague prompt using the four-part recipe.',
                    'instructions' => sb_p('Think of a real task from your job that an AI assistant could help with. Write the vague prompt most people would use, then rewrite it using context, task, format and an example. Finish with two things you would check in the answer.') .
                        sb_keypoints('Include', ['The weak prompt (one line)', 'Your improved prompt', 'Two checks you would make before using the result', 'No personal, customer or confidential data']) .
                        sb_note('tip', 'Keep it general', sb_p('Use placeholders like “Customer A” or “[product]” instead of real details.'))],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Strengths and limits', 'Writing good prompts', 'Checking AI output', 'What never to paste in', 'Being open about AI use', 'Approved tools'], 'questions' => [
                    ['mc', 'Which task is the best fit for an AI assistant?',
                        'Drafting a first version of an email from your own notes', ['Deciding which supplier wins a contract', 'Confirming the exact terms of a new law', 'Choosing which employee gets a promotion'],
                        'Drafting from material you provide plays to AI’s strengths, and you can easily check the result. Decisions and exact legal facts need people and original sources.'],
                    ['mc', 'Which prompt is most likely to get a useful answer?',
                        'A prompt that gives context, a clear task, the format wanted and an example', ['A one-line prompt, so the tool has freedom', 'A prompt written in technical language', 'A prompt that repeats the request several times'],
                        'Context, task, format and examples remove guesswork, so the first draft is much closer to what you need.'],
                    ['tf', 'If an AI answer includes a precise figure and a named source, you can trust it without checking.', false,
                        'AI tools can invent figures and references that look convincing. Always check facts you rely on against the original source.'],
                    ['mc', 'You need help wording a reply to a customer complaint. What is the safest approach?',
                        'Use an approved tool and replace the customer’s name and details with placeholders', ['Paste the full complaint into a free public AI website', 'Paste it into your personal AI account at home', 'Forward the complaint to a friend who uses AI tools'],
                        'Customer details must stay out of public tools. Use an approved tool and remove identifying details wherever you can.'],
                    ['tf', 'If AI drafted a large part of a report you are sharing, it is good practice to say so.', true,
                        'Being open about significant AI use builds trust. A short note such as “drafted with AI, reviewed by me” is enough.'],
                    ['mc', 'You want to try a new AI browser extension for work. What should you do first?',
                        'Check with IT whether it is approved', ['Install it and remove it if it causes problems', 'Ask a colleague who already uses it', 'Use it only for confidential tasks'],
                        'New tools can read or send data in ways you cannot see. Only approved tools should be used for company work.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Ask well, check everything, protect data',
                        'Three habits for using AI with confidence.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Ask well</strong>: give context, a clear task, the format you want and an example.',
                        '<strong>Check everything</strong>: facts, figures, references and tone, against reliable sources.',
                        '<strong>Protect data</strong>: no personal or confidential information in public tools. Use approved tools with your work account.',
                        '<strong>Be open</strong>: say when AI played a significant part in your work.',
                    ])) .
                    sb_keypoints('Before you use any AI output, ask yourself', [
                        'Have I checked every fact I am relying on?',
                        'Would I be happy to put my name to this?',
                        'Did I keep sensitive data out of the tool?',
                    ]) .
                    sb_p('Thank you for completing Using AI at Work. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about using AI tools safely?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
