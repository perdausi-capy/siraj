<?php
// Code of Conduct & Ethics: values, conflicts of interest, gifts and hospitality, speaking up and respect (Compliance & Policy).
return [
    'shortname' => 'CND201',
    'fullname' => 'Code of Conduct & Ethics',
    'category' => 'compliance',
    'mandatory' => true,
    'minutes' => 40,
    'cover' => 'img/photo/conduct.jpg',
    'summary' => 'Our Code of Conduct sets out how we do business and how we treat each other, in every office and with every client. In 40 minutes you will learn how our values apply to real decisions, how to handle conflicts of interest, gifts and hospitality, and how to speak up safely when something is wrong.',
    'outcomes' => [
        'Apply our four values (integrity, respect, accountability and care) to everyday decisions',
        'Recognise and declare a conflict of interest',
        'Decide whether a gift or hospitality can be accepted, declared or must be refused',
        'Speak up about a concern, knowing you are protected from retaliation',
        'Treat colleagues, clients and suppliers with respect, and recognise harassment',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why the Code matters, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Code of Conduct &amp; Ethics', 'Doing the right thing, every day',
                        'Most ethical problems do not arrive with a warning label. They look like an ordinary favour, a generous gift or a joke that goes too far. This course helps you spot them.') .
                    sb_stats([
                        ['4', 'values that guide every decision: integrity, respect, accountability and care'],
                        ['Every year', 'all employees complete this course and confirm they follow the Code'],
                        ['40 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What our values mean when a decision is not clear-cut',
                        'How to spot and declare a conflict of interest',
                        'When a gift or invitation is fine, when to declare it and when to say no',
                        'How to raise a concern, and how you are protected when you do',
                        'What respect at work looks like, and what crosses the line',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three lessons with realistic situations from across our offices.'],
                        ['Practise', 'Vote in a quick poll, discuss a grey area and work through a short case.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought. Your certificate appears once every step is done.'],
                    ]) .
                    sb_note('info', 'The Code applies to everyone', sb_p('The Code applies to every employee, at every level and in every country, and to contractors working on our behalf. Where local law sets a stricter rule, we follow the stricter rule.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: the right call', 'package' => sb_h5p('multiple-choice-2-6', 'The right call', [
                    'question' => '<p>A supplier you are evaluating sends you an expensive watch with a note saying, “Looking forward to working together.” What should you do?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Politely return it, and record it in the gifts register.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. A valuable gift during a selection process could influence, or look like it influences, your decision.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Keep it, as long as it does not change your decision.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Even if your decision is not affected, it will look as if it was. Perception matters too.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>Give it to a colleague who is not involved in the selection.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Passing it on does not solve the problem. The gift was still offered to influence the company.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Our values in practice',
            'summary' => 'What integrity, respect, accountability and care look like when the answer is not obvious.',
            'items' => [
                ['type' => 'page', 'name' => 'Our values in practice', 'content' =>
                    sb_banner('Lesson 1', 'Values are tested in grey areas', 'It is easy to act well when the right answer is obvious. Our values matter most when it is not.') .
                    sb_columns([
                        ['Integrity', sb_p('We are honest with clients and each other. We do not hide mistakes, inflate hours or make promises we cannot keep.')],
                        ['Respect', sb_p('We treat everyone with dignity, whatever their role, background, nationality or beliefs.')],
                    ]) .
                    sb_columns([
                        ['Accountability', sb_p('We own our decisions and their results. When something goes wrong, we say so early and help fix it.')],
                        ['Care', sb_p('We look out for colleagues, clients and the communities we work in, and we take safety and wellbeing seriously.')],
                    ]) .
                    '<h3>Four questions to ask yourself</h3>' .
                    sb_steps([
                        ['Is it legal?', 'If not, stop. If you are not sure, ask Legal or Compliance before you act.'],
                        ['Does it follow the Code and our policies?', 'Our policies often set a higher bar than the law.'],
                        ['Would I be comfortable if it were public?', 'Imagine it reported in the news, or explained to your family.'],
                        ['Does it fit our values?', 'Would a colleague you respect make the same choice?'],
                    ]) .
                    sb_note('tip', 'If in doubt, ask', sb_p('If any answer is “no” or “I am not sure”, pause and ask your manager or the Compliance team. Asking is never a sign of weakness.')) .
                    sb_quote('It takes 20 years to build a reputation and five minutes to ruin it. If you think about that, you’ll do things differently.', 'Warren Buffett, investor')],
            ],
        ],
        [
            'name' => 'Lesson 2 · Conflicts, gifts and hospitality',
            'summary' => 'Keeping personal interests separate from company decisions.',
            'items' => [
                ['type' => 'book', 'name' => 'Conflicts of interest and gifts', 'chapters' => [
                    ['What a conflict of interest is', sb_banner('Lesson 2', 'When personal and professional meet', 'A conflict of interest is not wrongdoing in itself. Hiding one is.') .
                        sb_p('A conflict of interest arises when your personal interests, or those of people close to you, could influence, or appear to influence, the decisions you make for the company.',
                            'Conflicts are common and usually easy to manage once they are declared. The problem is a conflict that nobody knows about.') .
                        sb_keypoints('Common examples', [
                            'A family member works for, or owns, a supplier or client you deal with',
                            'You have a second job or business that overlaps with our work',
                            'You hold shares in a company that bids for our contracts',
                            'You are involved in hiring or managing a close friend or relative',
                        ]) .
                        sb_note('warn', 'Appearance counts', sb_p('Even if you are sure you would be fair, others may not see it that way. If a reasonable person could think your judgement is affected, declare it.'))],
                    ['Declaring a conflict', sb_p('Declaring is simple and protects you. Once a conflict is recorded, your manager can make sure decisions are taken fairly.') .
                        sb_steps([
                            ['Tell your manager', 'As soon as you are aware of the conflict, not after a decision is made.'],
                            ['Record it', 'Complete the conflict of interest form on the intranet.'],
                            ['Step back where needed', 'Your manager may ask someone else to take the decision, or to review your work.'],
                            ['Update it', 'If your situation changes, update your declaration. All declarations are reviewed every year.'],
                        ]) .
                        sb_scenario('Omar works in procurement in Riyadh. A tender for office cleaning includes a bid from a company owned by his cousin. The bid is the cheapest, and Omar is sure it is the best value.',
                            'What should Omar do? (Answer: declare the relationship to his manager before the evaluation and step out of the decision. If the cousin’s company is best value, it can still win through a fair process.)')],
                    ['Gifts and hospitality', sb_p('Modest gifts and hospitality can be a normal part of building business relationships. They become a problem when they are lavish, frequent, secret or offered at a sensitive time.') .
                        sb_columns([
                            ['Usually fine', sb_p('Low-value items such as branded pens or diaries, a box of dates or sweets at Eid or the new year, or a working lunch.')],
                            ['Declare first', sb_p('Anything above the value limit in the Gifts and Hospitality Policy, invitations to events or dinners, and anything involving travel.')],
                            ['Always refuse', sb_p('Cash or cash equivalents such as gift cards, anything offered during a tender or negotiation, and anything given in secret.')],
                        ]) .
                        sb_note('warn', 'Public officials', sb_p('Gifts and hospitality to or from government officials carry extra risk under anti-bribery laws. Always get approval from Compliance first, whatever the value.'))],
                    ['Saying no politely', sb_p('Refusing a gift does not have to damage a relationship. Most people understand when you explain it simply and warmly.') .
                        sb_dodont(
                            ['Thank the person for the thought', 'Explain that our policy does not allow you to accept', 'Record refused gifts in the gifts register', 'Ask Compliance if you are unsure'],
                            ['Accept now and decide later', 'Ask for a gift to be sent to your home instead', 'Split a large gift into smaller ones to stay under the limit', 'Keep quiet because you are worried about offending someone']) .
                        sb_quote('The standard you walk past is the standard you accept.', 'Lieutenant General David Morrison, former Chief of the Australian Army')],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: gifts and conflicts', 'package' => sb_h5p('filltheblanks', 'Gifts and conflicts', [
                    'text' => '<p>Fill in the missing words from this lesson.</p>',
                    'questions' => ['<p>A conflict of interest should be *declared* as soon as you are aware of it.</p><p>Gift cards count as cash *equivalents* and must always be refused.</p><p>Gifts offered during a *tender* must be refused, whatever their value.</p><p>Refused gifts should still be recorded in the gifts *register*.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Speaking up and respect',
            'summary' => 'How to raise a concern safely, and how we treat each other.',
            'items' => [
                ['type' => 'page', 'name' => 'Speaking up', 'content' =>
                    sb_banner('Lesson 3', 'Silence lets problems grow', 'Raising a concern early protects colleagues, clients and the company. You will be supported when you do.') .
                    sb_p('If you see or suspect something that breaks the law, the Code or our policies, we want to know. You do not need proof. A genuine, honest concern is enough.') .
                    sb_columns([
                        ['Your manager', sb_p('Usually the quickest route, for most concerns.')],
                        ['People team or Compliance', sb_p('If the concern involves your manager, or you would rather talk to someone outside your team.')],
                        ['Speak Up line', sb_p('A confidential phone and web service, available at any time and in several languages. You can stay anonymous.')],
                    ]) .
                    sb_note('info', 'No retaliation', sb_p('Nobody will lose their job, be demoted or be treated unfairly for raising a genuine concern, even if it turns out to be mistaken. Retaliation against someone who speaks up is itself a serious breach of the Code and is a disciplinary matter.')) .
                    sb_faq([
                        ['What if I am not sure it is a real problem?', 'Raise it anyway. It is better to ask and be told all is well than to stay silent about something serious.'],
                        ['Will my name be shared?', 'Only with people who need to know to investigate. If you report anonymously through the Speak Up line, you can still answer follow-up questions without giving your name.'],
                        ['What happens after I report?', 'A trained investigator reviews the concern, looks at the facts and takes action where needed. You will be told when the case is closed, though some details may stay confidential.'],
                        ['What if someone treats me badly after I report?', 'Report it straight away. Retaliation is treated as seriously as the original concern.'],
                    ])],
                ['type' => 'page', 'name' => 'Treating people with respect', 'content' =>
                    sb_banner('Respect at work', 'Everyone belongs here', 'We work with people from dozens of countries and backgrounds. Respect is what makes that work.') .
                    sb_p('We do not tolerate harassment, bullying or discrimination of any kind, whether by colleagues, managers, clients or suppliers. This applies in the office, on client sites, at work events and online.',
                        'Harassment is unwanted behaviour that violates someone’s dignity or creates an intimidating or offensive environment. What matters is the effect on the other person, not the intention behind it.') .
                    sb_dodont(
                        ['Listen to different views and let people finish', 'Use people’s names and titles correctly', 'Respect religious practices, such as prayer times and fasting', 'Speak up, or check in privately, when you see someone treated badly'],
                        ['Make jokes about nationality, religion, gender or age', 'Shout at, belittle or exclude colleagues', 'Share offensive messages or images, even in private chats', 'Ignore behaviour because “that is just how they are”']) .
                    sb_scenario('In a team chat, a senior colleague keeps making jokes about Priya’s accent. Others add laughing emojis. Priya has stopped posting in the chat. James notices but is new and does not want to cause trouble.',
                        'What could James do? (Answer: check in with Priya privately, and raise it with his manager or the People team. He could also say, simply, “Let’s keep it professional.” Staying silent tells the colleague it is acceptable.)') .
                    sb_note('summary', 'Remember', sb_ul([
                        'Impact matters more than intention',
                        'Respect applies everywhere we work, including online',
                        'Bystanders can make a real difference by speaking up',
                    ]))],
                ['type' => 'glossary', 'name' => 'Ethics terms', 'intro' => 'Plain-language definitions of the words used in this course.', 'entries' => [
                    ['Conflict of interest', '<p>When personal interests could influence, or appear to influence, decisions you make for the company.</p>'],
                    ['Hospitality', '<p>Meals, events, entertainment or travel offered or received as part of a business relationship.</p>'],
                    ['Cash equivalent', '<p>Anything that works like cash, such as gift cards, vouchers or prepaid cards.</p>'],
                    ['Retaliation', '<p>Any negative treatment of someone because they raised a concern in good faith.</p>'],
                    ['Harassment', '<p>Unwanted behaviour that violates someone’s dignity or creates an intimidating, hostile or offensive environment.</p>'],
                    ['Public official', '<p>Anyone who works for a government, a government-owned company or a public international organisation.</p>'],
                ]],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Test your judgement on grey areas and work through a short case.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: accept, declare or refuse?', 'intro' => 'A client invites you to a football final, with flights and two nights in a hotel. What would you do? Results are shown after you answer.',
                    'options' => ['Accept: it is good for the relationship', 'Declare it and ask for approval before deciding', 'Refuse it straight away', 'Not sure']],
                ['type' => 'forum', 'name' => 'Discuss a grey area', 'intro' => 'Share a situation where the right thing to do was not obvious. Leave out names and anything that could identify a client or colleague.',
                    'posts' => [['Welcome: when was it not clear-cut?', 'Describe a situation, real or imagined, where our values pulled in different directions. What did you weigh up, and what would you do? Reply to at least one colleague with your view.']]],
                ['type' => 'assign', 'name' => 'Case study: the supplier dinner', 'dueindays' => 21, 'words' => 250,
                    'intro' => 'Apply the Code to a realistic case.',
                    'instructions' => sb_p('Fatima manages a contract with an IT supplier in Doha. The contract is up for renewal next month. The supplier’s account manager invites Fatima and her husband to an expensive dinner to “thank her for a great year”.',
                        'In 250 words or fewer, explain what Fatima should do and why.') .
                        sb_keypoints('Include', ['Which parts of the Code apply', 'Whether the timing makes a difference', 'What Fatima should say to the supplier', 'Who she should tell, and what she should record'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Conduct check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Our values in practice', 'Conflicts of interest and gifts', 'Speaking up', 'Treating people with respect'], 'questions' => [
                    ['mc', 'Your brother has just joined a supplier your team works with. What should you do?',
                        'Declare it to your manager and record it on the conflict of interest form', ['Nothing, as long as you stay fair', 'Ask your brother not to mention it', 'Stop working with the supplier without telling anyone'],
                        'Declaring a conflict early protects you and lets your manager make sure decisions stay fair.'],
                    ['mc', 'A client offers you a gift card as a thank-you at the end of a project. What should you do?',
                        'Politely refuse it and record it in the gifts register', ['Accept it if the value is small', 'Accept it and share it with the team', 'Accept it and declare it later'],
                        'Gift cards are cash equivalents and must always be refused, whatever the value.'],
                    ['tf', 'You need proof before you raise a concern about possible wrongdoing.', false,
                        'A genuine, honest concern is enough. Investigators will look at the facts.'],
                    ['tf', 'Someone who raises a genuine concern that turns out to be mistaken is still protected from retaliation.', true,
                        'Protection depends on raising the concern honestly, not on being right.'],
                    ['mc', 'A colleague says their jokes about someone’s religion are “only banter”. Which statement is correct?',
                        'What matters is the effect on the other person, not the intention', ['Jokes are fine if nobody complains', 'It is only harassment if it happens in the office', 'Banter in private chats is not covered by the Code'],
                        'Harassment is judged by its impact, and the Code applies everywhere we work, including online.'],
                    ['mc', 'Which gift is usually fine to accept without declaring it?',
                        'A box of dates from a client at Eid', ['Two tickets to a final with flights included', 'A voucher for a luxury shop', 'A watch from a supplier during a tender'],
                        'Low-value, customary gifts are usually fine. Travel, vouchers and gifts during a tender are not.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Pause, declare, speak up', 'Three habits that keep you, and the company, on the right side of the line.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Pause</strong> when a decision feels uncomfortable, and ask the four questions.',
                        '<strong>Declare</strong> conflicts of interest and gifts early, and refuse anything lavish, secret or offered at a sensitive time.',
                        '<strong>Speak up</strong> about anything that is wrong. You are protected when you do.',
                        '<strong>Respect</strong> everyone, everywhere we work. Impact matters more than intention.',
                    ])) .
                    sb_note('tip', 'Keep the Code close', sb_p('The full Code of Conduct, the Gifts and Hospitality Policy and the Speak Up line details are on the intranet under Policies.')) .
                    sb_p('Thank you for completing Code of Conduct &amp; Ethics. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Conduct check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about raising a concern?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
