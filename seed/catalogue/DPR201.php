<?php
// Data Protection Essentials: mandatory compliance course. Personal data, lawful basis, minimisation, safe storage and
// sharing, data subject requests and breach reporting. Jurisdiction-neutral.
return [
    'shortname' => 'DPR201',
    'fullname' => 'Data Protection Essentials',
    'category' => 'compliance',
    'mandatory' => true,
    'minutes' => 40,
    'cover' => 'img/photo/dpe.jpg',
    'summary' => 'Every day you handle information about real people: customers, colleagues and suppliers. In 40 minutes you will learn what counts as personal data, how to collect and share it fairly, how to answer a request from the person it belongs to, and how to spot and report a data breach fast.',
    'outcomes' => [
        'Recognise personal data, including the more sensitive kinds',
        'Explain in plain words why the company is allowed to use someone’s data',
        'Collect only what you need, and store and share it safely',
        'Pass on a data subject request straight away',
        'Recognise a data breach and report it within the hour',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why data protection matters to everyone, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Data Protection Essentials', 'Treat their data as you would want yours treated',
                        'Behind every spreadsheet row is a person. Handling their information with care keeps their trust, and keeps the company on the right side of the law.') .
                    sb_stats([
                        ['3', 'short lessons, each with everyday workplace examples'],
                        ['1 hour', 'is how quickly you should report a possible data breach'],
                        ['40 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What personal data is, and which kinds need extra care',
                        'The lawful reasons for using personal data, in plain words',
                        'How to collect only what you need and keep it safe',
                        'What to do when someone asks to see or delete their data',
                        'How to recognise a data breach and who to tell',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons with everyday examples from offices, sales and HR.'],
                        ['Practise', 'Answer a quick poll, share an example with colleagues and write one short breach report.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought and download your certificate.'],
                    ]) .
                    sb_note('info', 'Which law applies?', sb_p('Data protection laws differ from country to country. The GDPR in Europe and the data protection laws in the UAE and Saudi Arabia all share the same core ideas taught here. If you are unsure about a local rule, ask the Data Protection Officer or the legal team.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: is it personal data?', 'package' => sb_h5p('multiple-choice-2-6', 'Is it personal data', [
                    'question' => '<p>Which of these is personal data?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>A work email address such as omar.hassan@company.example.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. A work email that names a person identifies them, so it is personal data.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>The total number of visitors to the office last month.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>A total that cannot be traced back to anyone is not personal data.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>The general enquiries address, such as info@ followed by the company domain.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>A shared mailbox that does not identify a person is usually not personal data.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · What counts as personal data',
            'summary' => 'Personal data, the more sensitive kinds, and the reasons the company is allowed to use it.',
            'items' => [
                ['type' => 'book', 'name' => 'Personal data and lawful basis', 'chapters' => [
                    ['What personal data is', sb_banner('Lesson 1', 'If it points to a person, it counts', 'Personal data is any information that identifies someone, on its own or together with other details.') .
                        sb_p('Names, phone numbers and addresses are obvious. Less obvious are an employee number, a car number plate, a photo from the office event, a voice recording or the location data from a company phone.',
                            'Information can also become personal when it is combined. “A sales manager in the Riyadh office who joined in March” may point to exactly one person, even without a name.') .
                        sb_columns([
                            ['Personal data', sb_ul(['Name, email, phone number', 'ID or passport number', 'Photos and CCTV footage', 'Performance reviews', 'IP address or device ID'])],
                            ['Usually not personal data', sb_ul(['Company-wide totals and averages', 'A shared mailbox like info@', 'Truly anonymous survey results', 'The company’s own address'])],
                        ])],
                    ['Data that needs extra care', sb_p('Some personal data could cause real harm if it were lost or misused. Most laws call it sensitive or special category data, and set stricter rules for it.') .
                        sb_keypoints('Handle these with extra care', [
                            'Health and medical information, including sick notes',
                            'Religious or philosophical beliefs',
                            'Racial or ethnic origin',
                            'Biometric data such as fingerprints used for door entry',
                            'Criminal records and background checks',
                            'Financial details such as bank account and salary information',
                        ]) .
                        sb_note('warn', 'Ask before you share', sb_p('If a task involves sensitive data and you are not sure who should see it, stop and ask your manager or the Data Protection Officer first.'))],
                    ['Lawful basis in plain terms', sb_p('The company may only use personal data when it has a good, lawful reason. Laws word these reasons differently, but they come down to a few simple questions.') .
                        sb_steps([
                            ['Do we need it for a contract?', 'To pay Priya’s salary, we need her bank details. To deliver an order, we need the customer’s address.'],
                            ['Does the law require it?', 'Tax records, visa paperwork and health and safety reports.'],
                            ['Did the person agree?', 'A customer ticks a box to receive the newsletter. Consent must be clear, freely given and easy to withdraw.'],
                            ['Is there a fair business reason?', 'Keeping a log of who enters the building, where it is reasonable and the person would expect it.'],
                            ['Is someone’s life or safety at risk?', 'Sharing a colleague’s allergy details with paramedics in an emergency.'],
                        ]) .
                        sb_note('tip', 'The simple test', sb_p('Ask yourself: “Would this person be surprised to learn we are using their data this way?” If the answer is yes, check before you go ahead.'))],
                ]],
                ['type' => 'glossary', 'name' => 'Data protection terms', 'intro' => 'Plain-language definitions of the words used in this course.', 'entries' => [
                    ['Personal data', '<p>Any information that identifies a living person, directly or when combined with other details.</p>'],
                    ['Sensitive data', '<p>Personal data that could cause serious harm if misused, such as health, religion or biometric data. Also called special category data.</p>'],
                    ['Data subject', '<p>The person the data is about: a customer, colleague, job applicant or supplier contact.</p>'],
                    ['Lawful basis', '<p>The legal reason that allows the company to use personal data, such as a contract, a legal duty or consent.</p>'],
                    ['Data minimisation', '<p>Collecting and keeping only the personal data you actually need for the task.</p>'],
                    ['Data breach', '<p>Any event where personal data is lost, stolen, destroyed, changed or seen by someone who should not see it.</p>'],
                    ['Data Protection Officer (DPO)', '<p>The person in the company who advises on data protection and is the contact point for regulators and data subjects.</p>'],
                ]],
            ],
        ],
        [
            'name' => 'Lesson 2 · Collect, store and share',
            'summary' => 'Collecting only what you need, and keeping it safe when you store it and share it.',
            'items' => [
                ['type' => 'page', 'name' => 'Collect only what you need', 'content' =>
                    sb_banner('Lesson 2', 'Less data, less risk', 'Data you never collected can never be lost, leaked or misused.') .
                    sb_p('Before you collect personal data, ask what you need it for. Then collect that, and nothing more. Keep it only as long as the purpose lasts, then delete it in line with the company’s retention schedule.') .
                    sb_dodont(
                        ['Ask only the questions the task needs', 'Tell people why you are collecting their data', 'Delete old records when the retention period ends', 'Use anonymous or summary data for reports where you can'],
                        ['Collect ID copies “just in case”', 'Add extra fields to a form because they might be useful one day', 'Keep personal copies of spreadsheets after a project', 'Reuse data for a new purpose without checking']) .
                    sb_scenario('Fatima is organising a team lunch. Her sign-up form asks for full name, date of birth, nationality, phone number and dietary needs.',
                        'Which questions does she really need? (Answer: name and dietary needs. Date of birth and nationality add risk and serve no purpose here.)') .
                    sb_quote('If you don’t need it, don’t collect it. If you collected it, protect it. When you are done, delete it.', 'A common rule among privacy professionals')],
                ['type' => 'page', 'name' => 'Storing and sharing safely', 'content' =>
                    sb_banner('Lesson 2', 'Right place, right people', 'Keep personal data in approved systems and share it only with those who need it.') .
                    sb_keypoints('Storing personal data', [
                        'Use approved company systems, never personal drives, USB sticks or personal email',
                        'Limit access to the people who need it, and remove access when they move on',
                        'Lock your screen and keep printed documents out of sight',
                        'Shred paper records and use approved disposal for old devices',
                    ]) .
                    '<h3>Before you share</h3>' .
                    sb_steps([
                        ['Check the need', 'Does the person really need this data to do their job? Can you send less?'],
                        ['Check the recipient', 'Is the address right? Autocomplete sends many emails to the wrong person.'],
                        ['Use a secure method', 'Share a link to a company system with access controls rather than attaching a file.'],
                        ['Check external sharing', 'Sending data to a supplier or another country may need a contract or approval. Ask first.'],
                    ]) .
                    sb_note('warn', 'Watch the CC line', sb_p('Sending a group email with everyone’s address in CC shares their emails with each other. Use BCC or a mailing tool for external groups.')) .
                    sb_faq([
                        ['Can I take customer files home to work on?', 'Only on a company device through approved systems. Never email files to yourself or print them at home.'],
                        ['A colleague from another team asks for a customer list. Can I send it?', 'Check why they need it and whether it is approved. Share only what they need, through a company system.'],
                        ['Is it fine to share data on a messaging app?', 'Only the apps the company has approved for work. Personal chat apps are not.'],
                    ])],
                ['type' => 'h5p', 'name' => 'Practice: data handling checklist', 'package' => sb_h5p('filltheblanks', 'Data handling checklist', [
                    'text' => '<p>Fill in the missing words from this lesson.</p>',
                    'questions' => ['<p>Collect only the data you *need* for the task.</p><p>Store personal data only in *approved* company systems.</p><p>For external group emails, use *BCC* so addresses stay private.</p><p>Delete records when the *retention* period ends.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Rights and breaches',
            'summary' => 'What to do when someone asks about their data, and how to spot and report a data breach.',
            'items' => [
                ['type' => 'page', 'name' => 'Data subject requests', 'content' =>
                    sb_banner('Lesson 3', 'It’s their data', 'People have the right to know what the company holds about them and, in many cases, to correct or delete it.') .
                    sb_columns([
                        ['What people can ask for', sb_ul(['A copy of their data', 'Corrections to wrong data', 'Deletion, in some cases', 'To stop marketing messages', 'To know who their data is shared with'])],
                        ['What you do', sb_ul(['Recognise the request, in any wording', 'Pass it on to the DPO the same day', 'Do not reply with data yourself', 'Do not delete anything yourself'])],
                    ]) .
                    sb_p('A request does not need special words or a form. “Can you tell me what you have on file about me?” in a phone call or chat message is a valid request.') .
                    sb_note('info', 'The clock starts straight away', sb_p('Most laws give the company a fixed time to answer, often about a month. The time starts when anyone in the company receives the request, so pass it on the same day.')) .
                    sb_scenario('James in customer service gets an email: “Please delete everything you hold about me and stop calling me.”',
                        'What should he do? (Answer: forward it to the DPO the same day and let the customer know it is being handled. He should not delete records himself.)')],
                ['type' => 'page', 'name' => 'Spotting and reporting a breach', 'content' =>
                    sb_banner('Lesson 3', 'Report first, fix second', 'A fast report turns a serious breach into a contained one.') .
                    sb_p('A data breach is not only a cyber attack. Most breaches are everyday mistakes. They all need reporting, however small they seem.') .
                    sb_keypoints('Common data breaches', [
                        'An email with personal data sent to the wrong person',
                        'A lost or stolen laptop, phone or USB stick',
                        'Printed HR documents left on a printer or in a meeting room',
                        'A shared folder open to people who should not see it',
                        'A colleague looking at records they have no reason to see',
                    ]) .
                    '<h3>What to do</h3>' .
                    sb_steps([
                        ['Stop', 'If you can, stop it getting worse: recall the email, ask the recipient to delete it, close the folder access.'],
                        ['Report', 'Tell the DPO or the IT security desk within the hour, even if you are not sure it is a breach.'],
                        ['Record', 'Note what happened, when, which data and how many people were affected.'],
                        ['Help', 'Answer questions from the DPO and keep the details confidential.'],
                    ]) .
                    sb_note('warn', 'Why speed matters', sb_p('Many laws give the company only a few days, sometimes as little as 72 hours, to tell the regulator about a serious breach. That clock can start when you notice it.')) .
                    sb_note('tip', 'Nobody is blamed for reporting', sb_p('Honest mistakes happen. Hiding one is what causes harm. You will be thanked for reporting quickly.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share what you have seen, and practise writing a breach report.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: where does data slip most?', 'intro' => 'Pick the one you think happens most often in your area. Results are shown after you answer.',
                    'options' => ['Emails sent to the wrong person', 'Files saved in the wrong place', 'Paper left on desks or printers', 'Too many people with access to a folder']],
                ['type' => 'forum', 'name' => 'Share a good data habit', 'intro' => 'Share one habit that helps you or your team protect personal data. Never include real personal data in your post.',
                    'posts' => [['Welcome: what works for you?', 'What is one small habit that helps you keep personal data safe? For example, checking the recipient twice before sending, or a monthly clean-up of shared folders. Your idea could help a colleague.']]],
                ['type' => 'assign', 'name' => 'Write a breach report', 'dueindays' => 21, 'words' => 200,
                    'intro' => 'Practise writing the short report the Data Protection Officer needs.',
                    'instructions' => sb_p('Imagine you sent a spreadsheet with ten colleagues’ salary details to the wrong Omar in the address book. Write the report you would send to the DPO, in 200 words or fewer.') .
                        sb_keypoints('Include', ['What happened and when you noticed', 'Which data was involved and how many people', 'Who received it, and whether they are inside or outside the company', 'What you have already done to contain it'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Personal data and lawful basis', 'Collect only what you need', 'Storing and sharing safely', 'Data subject requests', 'Spotting and reporting a breach'], 'questions' => [
                    ['mc', 'Which of these is personal data?',
                        'A photo of a colleague from the office event', ['The average age of all staff', 'The company’s registered address', 'A shared info@ mailbox'],
                        'A photo identifies a person, so it is personal data. Totals, company details and shared mailboxes usually are not.'],
                    ['mc', 'Which of these is sensitive data that needs extra care?',
                        'A colleague’s sick note', ['A colleague’s job title', 'A colleague’s work phone extension', 'The team’s meeting schedule'],
                        'Health information is sensitive data. It needs extra care and should only be seen by people who need it.'],
                    ['tf', 'It is good practice to collect extra personal details on a form in case they are useful later.', false,
                        'Collect only what you need for the task. Extra data adds risk and serves no purpose.'],
                    ['mc', 'A customer phones and asks what information the company holds about them. What should you do?',
                        'Pass the request to the Data Protection Officer the same day', ['Tell them they must send a formal letter', 'Email them a copy of their records yourself', 'Ignore it unless they ask again'],
                        'A request can be made in any form. Pass it on straight away, because the time to answer starts when the company receives it.'],
                    ['mc', 'You realise you emailed a file with personal data to the wrong person. What is the right first step?',
                        'Try to contain it and report it to the DPO within the hour', ['Wait to see if the person complains', 'Delete the sent email and say nothing', 'Report it at the end of the month'],
                        'Contain it if you can and report it quickly. The company may have only a few days to tell the regulator.'],
                    ['tf', 'Data protection laws such as the GDPR and the UAE and Saudi laws share similar core principles.', true,
                        'The details differ, but the core ideas are the same: use data fairly, keep only what you need, keep it safe and respect people’s rights.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Need it, protect it, report it', 'Three habits that cover most of data protection.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Need it:</strong> collect and keep only the personal data the task needs.',
                        '<strong>Protect it:</strong> store it in approved systems and share it only with people who need it.',
                        '<strong>Respect it:</strong> pass on any request about someone’s data the same day.',
                        '<strong>Report it:</strong> tell the DPO about any possible breach within the hour.',
                    ])) .
                    sb_p('Thank you for completing Data Protection Essentials. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about spotting a data breach?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
