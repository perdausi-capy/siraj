<?php
// Workplace Health & Safety: mandatory safety course. Common hazards, the five-step risk assessment, reporting incidents
// and near misses, emergency basics and everyone's responsibilities.
return [
    'shortname' => 'HSW201',
    'fullname' => 'Workplace Health & Safety',
    'category' => 'safety',
    'mandatory' => true,
    'minutes' => 40,
    'cover' => 'img/photo/hse.jpg',
    'summary' => 'Most workplace injuries come from ordinary things: a wet floor, a heavy box, a trailing cable, a hot afternoon. In 40 minutes you will learn to spot the common hazards, assess a risk in five steps, report incidents and near misses, and know what to do in an emergency.',
    'outcomes' => [
        'Recognise the most common hazards in offices, warehouses and on site',
        'Carry out a simple risk assessment in five steps',
        'Report incidents and near misses quickly and clearly',
        'Know what to do when the alarm sounds or someone is hurt',
        'Explain what you, your manager and the company are each responsible for',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why safety is part of everyone’s job, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Workplace Health & Safety', 'Everyone goes home safe',
                        'Safety is not paperwork. It is noticing the cable across the walkway, moving it, and telling someone. Small actions prevent most injuries.') .
                    sb_stats([
                        ['5', 'steps to assess any risk, at your desk or on site'],
                        ['Same day', 'is when every incident and near miss should be reported'],
                        ['40 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'The common hazards: slips and trips, lifting, screens, electricity and heat',
                        'How to assess a risk in five simple steps',
                        'Why reporting near misses matters as much as reporting injuries',
                        'What to do in a fire, an evacuation or a first aid emergency',
                        'Who is responsible for what',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons with examples from offices, warehouses and sites.'],
                        ['Practise', 'Answer a quick poll, share a hazard you have spotted and assess one real task.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought and download your certificate.'],
                    ]) .
                    sb_note('info', 'Local rules', sb_p('Safety laws differ by country and by site. This course covers the basics that apply everywhere. Always follow your site’s own rules and the instructions of your safety officer.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: spot the hazard', 'package' => sb_h5p('multiple-choice-2-6', 'Spot the hazard', [
                    'question' => '<p>You walk into the office kitchen. Which of these should you deal with first?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>Water spilled on the tiled floor by the sink.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. A wet hard floor is a classic slip hazard. Wipe it up or put out a sign, then report it if it keeps happening.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>A full recycling bin waiting to be emptied.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Untidy, but low risk. The wet floor could cause a fall right now.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>A notice board with an out-of-date poster.</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Worth updating, but not a hazard to anyone.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · Common hazards',
            'summary' => 'Slips and trips, manual handling, display screens, electricity and heat.',
            'items' => [
                ['type' => 'book', 'name' => 'Common workplace hazards', 'chapters' => [
                    ['Slips, trips and falls', sb_banner('Lesson 1', 'The everyday hazard', 'Slips and trips are among the most common causes of injury at work, in offices as much as on sites.') .
                        sb_p('Most falls happen on the level, not from height. A wet floor, a trailing cable, a box left in a corridor or a loose mat is all it takes.') .
                        sb_dodont(
                            ['Clean up spills straight away, or put out a warning sign', 'Keep walkways and fire exits clear', 'Route cables along walls or use cable covers', 'Use the handrail on stairs'],
                            ['Leave boxes or bags in corridors', 'Run on stairs or while carrying things', 'Stand on chairs to reach high shelves', 'Walk past a hazard thinking someone else will deal with it']) .
                        sb_note('tip', 'See it, sort it', sb_p('If you can fix a hazard safely in a few seconds, do it. If you cannot, make it visible and report it.'))],
                    ['Manual handling', sb_p('Manual handling means lifting, carrying, pushing or pulling. Back injuries often build up over time, not from one heavy lift.') .
                        '<h3>Before and during a lift</h3>' .
                        sb_steps([
                            ['Think first', 'Do you need to lift it at all? Can you use a trolley, or ask a colleague?'],
                            ['Check the load', 'Test the weight by lifting one corner. Check for sharp edges or loose contents.'],
                            ['Get a stable base', 'Feet apart, one slightly forward. Bend your knees, not your back.'],
                            ['Keep it close', 'Hold the load close to your waist, with the heaviest side nearest you.'],
                            ['Don’t twist', 'Move your feet to turn. Put the load down, then adjust its position.'],
                        ]) .
                        sb_scenario('Omar needs to move six boxes of printer paper from the delivery area to the store room on another floor. He plans to carry two at a time on the stairs to save time.',
                            'What would be safer? (Answer: use a trolley and the lift, or carry one box at a time. Carrying a load that blocks your view on stairs risks a fall.)')],
                    ['Display screen equipment', sb_p('If you use a computer for long periods, your set-up matters. Poor posture and long stretches without a break can lead to neck, back, wrist and eye strain.') .
                        sb_keypoints('A good workstation', [
                            'Top of the screen at or just below eye level, about an arm’s length away',
                            'Feet flat on the floor or on a footrest, back supported by the chair',
                            'Wrists straight while typing, elbows roughly level with the keyboard',
                            'A laptop on a stand with a separate keyboard and mouse for long sessions',
                            'Screen positioned to avoid glare from windows',
                        ]) .
                        sb_note('tip', 'The 20-20-20 habit', sb_p('Every 20 minutes, look at something about 20 feet (6 metres) away for 20 seconds. It gives your eyes a rest.')) .
                        sb_p('If you have pain or discomfort that lasts, tell your manager. You can ask for a workstation assessment.')],
                    ['Electricity and heat', sb_columns([
                            ['Electricity', sb_ul(['Check plugs and cables for damage before use', 'Do not overload sockets or chain extension leads', 'Keep drinks away from equipment', 'Never repair electrical equipment yourself', 'Take damaged equipment out of use and report it'])],
                            ['Heat', sb_ul(['Drink water regularly, before you feel thirsty', 'Take breaks in the shade or a cool room', 'Follow your site’s summer working hours and midday break rules', 'Wear light, loose clothing where allowed', 'Watch colleagues for signs of heat illness'])],
                        ]) .
                        sb_note('warn', 'Heat illness can be an emergency', sb_p('Headache, dizziness, confusion, heavy sweating or hot dry skin can be signs of heat exhaustion or heat stroke. Move the person somewhere cool, give water if they are alert, and call for first aid. If they are confused or collapse, call emergency services.')) .
                        sb_p('Many countries in the Gulf set a midday break for outdoor work in the summer months. Your site will tell you the dates and hours that apply.')],
                ]],
            ],
        ],
        [
            'name' => 'Lesson 2 · Assessing risk',
            'summary' => 'The five steps of a risk assessment, with a worked example.',
            'items' => [
                ['type' => 'page', 'name' => 'Five steps to risk assessment', 'content' =>
                    sb_banner('Lesson 2', 'Think before you start', 'A risk assessment is simply a careful look at what could cause harm, and what to do about it.') .
                    sb_columns([
                        ['Hazard', sb_p('Something that could cause harm: a wet floor, a heavy box, a live cable, a hot day.')],
                        ['Risk', sb_p('How likely it is that someone is harmed, and how badly. A wet floor in a busy corridor is a higher risk than one in a closed store room.')],
                    ]) .
                    '<h3>The five steps</h3>' .
                    sb_steps([
                        ['Identify the hazards', 'Walk around, look at the task and ask the people who do it what could go wrong.'],
                        ['Decide who might be harmed, and how', 'Staff, visitors, cleaners, contractors and anyone more at risk, such as new starters or pregnant colleagues.'],
                        ['Evaluate the risks and decide on controls', 'Remove the hazard if you can. If not, reduce it: safer equipment, barriers, training, protective equipment.'],
                        ['Record your findings', 'Write down the hazards, the controls and who is responsible. Share it with the people affected.'],
                        ['Review and update', 'Look again when something changes: new equipment, a new layout, an incident or near miss.'],
                    ]) .
                    sb_scenario('Priya’s team is setting up a stand at a trade exhibition. There are boxes to unload, cables for screens and a busy public area.',
                        'Using the five steps, what are two hazards and one control for each? (Example: trailing cables, covered with cable mats; heavy boxes, moved with a trolley.)') .
                    sb_faq([
                        ['Do I need to write a risk assessment for everything?', 'Your manager or safety officer is responsible for formal assessments. But you should always run through the five steps in your head before a new or unusual task.'],
                        ['What if I think a risk assessment is wrong or out of date?', 'Tell your manager or safety officer. The people who do the work often spot what the paperwork missed.'],
                        ['Can I refuse to do a task I believe is unsafe?', 'If you believe there is serious and immediate danger, stop and tell your manager straight away. You will not be blamed for raising a genuine safety concern.'],
                    ])],
                ['type' => 'url', 'name' => 'Further reading: managing risks', 'url' => 'https://www.hse.gov.uk/simple-health-safety/risk/index.htm',
                    'intro' => 'Simple guidance on risk assessment from the UK Health and Safety Executive.'],
                ['type' => 'h5p', 'name' => 'Practice: the five steps', 'package' => sb_h5p('filltheblanks', 'The five steps', [
                    'text' => '<p>Fill in the missing words from the five steps of risk assessment.</p>',
                    'questions' => ['<p>Step 1: identify the *hazards*.</p><p>Step 2: decide who might be *harmed* and how.</p><p>Step 3: evaluate the risks and decide on *controls*.</p><p>Step 4: *record* your findings.</p><p>Step 5: *review* and update.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · When things go wrong',
            'summary' => 'Reporting incidents and near misses, emergency basics and who is responsible for what.',
            'items' => [
                ['type' => 'page', 'name' => 'Reporting incidents and near misses', 'content' =>
                    sb_banner('Lesson 3', 'Today’s near miss is tomorrow’s injury', 'Every report helps fix a problem before someone gets hurt.') .
                    sb_columns([
                        ['Incident', sb_p('Something that caused harm or damage: a cut, a fall, a burn, damaged equipment.')],
                        ['Near miss', sb_p('Something that could have caused harm but didn’t: a box falling from a shelf just after someone walked past.')],
                    ]) .
                    '<h3>How to report</h3>' .
                    sb_steps([
                        ['Make it safe', 'Help anyone hurt and stop others walking into the same danger.'],
                        ['Tell your manager', 'As soon as you can, and the same day at the latest.'],
                        ['Fill in the report', 'Use the company incident form: what happened, where, when, who was involved and what you saw.'],
                        ['Follow up', 'Answer questions and help the team understand what needs to change.'],
                    ]) .
                    sb_note('info', 'No blame for reporting', sb_p('Reports are used to fix problems, not to blame people. Reporting your own near miss is a sign of a good safety culture.')) .
                    sb_quote('If you see something unsafe, you own it until you report it.', 'A common saying among safety professionals')],
                ['type' => 'page', 'name' => 'Emergency basics', 'content' =>
                    sb_banner('Lesson 3', 'Know before you need it', 'In an emergency there is no time to read instructions. Learn the basics now.') .
                    sb_keypoints('Know these for your workplace', [
                        'Where your nearest two fire exits are',
                        'Where the assembly point is',
                        'Who your fire wardens and first aiders are',
                        'Where the first aid kit and fire extinguishers are',
                        'The local emergency number',
                    ]) .
                    '<h3>When the fire alarm sounds</h3>' .
                    sb_dodont(
                        ['Leave straight away by the nearest safe exit', 'Close doors behind you if you can', 'Help visitors and colleagues who need assistance', 'Go to the assembly point and report to the warden'],
                        ['Stop to collect belongings', 'Use the lifts', 'Go back inside until you are told it is safe', 'Assume it is a drill']) .
                    sb_note('warn', 'If someone is hurt', sb_p('Make sure you are safe first. Call for a trained first aider and, if it is serious, call emergency services. Do not move the person unless they are in danger.'))],
                ['type' => 'page', 'name' => 'Everyone’s responsibilities', 'content' =>
                    sb_banner('Lesson 3', 'Safety is shared', 'The company, managers and every employee each have a part to play.') .
                    sb_columns([
                        ['The company', sb_ul(['Provides a safe workplace and safe equipment', 'Assesses risks and puts controls in place', 'Provides training and information', 'Provides first aid and emergency arrangements'])],
                        ['Managers', sb_ul(['Make sure risk assessments are done and followed', 'Act on reports and concerns', 'Make sure their team is trained', 'Lead by example'])],
                        ['You', sb_ul(['Take reasonable care of yourself and others', 'Follow training and safety instructions', 'Use protective equipment provided', 'Report hazards, incidents and near misses'])],
                    ]) .
                    sb_scenario('Fatima notices that the fire exit door on her floor has been propped open with a fire extinguisher for the last two days.',
                        'What should she do? (Answer: return the extinguisher to its place, close the door if it is a fire door, and report it to the fire warden or her manager.)')],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share what you have spotted, and practise a simple risk assessment.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: which hazard do you see most?', 'intro' => 'Pick the one you come across most often at work. Results are shown after you answer.',
                    'options' => ['Slips and trips', 'Lifting and carrying', 'Poor desk set-up', 'Damaged cables or equipment', 'Working in the heat']],
                ['type' => 'forum', 'name' => 'Share a hazard you have spotted', 'intro' => 'Describe a hazard or near miss you noticed and what was done about it. Do not name anyone involved.',
                    'posts' => [['Welcome: what did you spot?', 'Tell us about a hazard you noticed at work and how it was fixed, or how it could be. Your example could help a colleague spot the same thing.']]],
                ['type' => 'assign', 'name' => 'Assess a real task', 'dueindays' => 21, 'words' => 250,
                    'intro' => 'Practise the five steps on a task from your own work.',
                    'instructions' => sb_p('Choose one task you do regularly, such as moving stock, setting up a meeting room or working at your desk. Use the five steps to write a short risk assessment, in 250 words or fewer.') .
                        sb_keypoints('Include', ['The task and where it happens', 'At least two hazards', 'Who might be harmed and how', 'One control for each hazard', 'When you would review it'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Common workplace hazards', 'Five steps to risk assessment', 'Reporting incidents and near misses', 'Emergency basics', 'Everyone’s responsibilities'], 'questions' => [
                    ['mc', 'You need to lift a heavy box from the floor. What is the safest technique?',
                        'Bend your knees, keep the box close and avoid twisting', ['Keep your legs straight and bend at the waist', 'Lift quickly to get it over with', 'Hold it at arm’s length for balance'],
                        'Bend your knees, keep the load close to your waist and turn with your feet, not your back.'],
                    ['mc', 'What is the first step of a risk assessment?',
                        'Identify the hazards', ['Record your findings', 'Buy protective equipment', 'Review and update'],
                        'You cannot control a risk until you know what the hazards are.'],
                    ['tf', 'A near miss does not need to be reported because nobody was hurt.', false,
                        'Near misses show where the next injury could happen. Reporting them helps fix the problem first.'],
                    ['mc', 'The fire alarm sounds while you are in a meeting on the fourth floor. What should you do?',
                        'Leave by the nearest safe exit using the stairs and go to the assembly point', ['Take the lift to get out faster', 'Finish the meeting in case it is a drill', 'Go back to your desk to collect your laptop'],
                        'Leave straight away, never use the lifts, and report to the warden at the assembly point.'],
                    ['mc', 'A colleague working outdoors on a hot afternoon becomes dizzy and confused. What should you do?',
                        'Move them somewhere cool, call for first aid and call emergency services', ['Tell them to finish the job and rest later', 'Give them a hot drink', 'Leave them alone to sleep it off'],
                        'Confusion can be a sign of heat stroke, which is an emergency. Cool them down and get help fast.'],
                    ['tf', 'Health and safety is only the responsibility of managers and the safety team.', false,
                        'Everyone has a part to play: taking care of yourself and others, following instructions and reporting hazards.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Spot it, sort it, report it', 'Three habits that keep you and your colleagues safe.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Spot</strong> hazards: wet floors, heavy loads, poor desk set-up, damaged cables and heat.',
                        '<strong>Sort</strong> what you safely can, and use the five steps before new or unusual tasks.',
                        '<strong>Report</strong> every incident and near miss the same day.',
                        '<strong>Know</strong> your exits, assembly point, first aiders and the emergency number.',
                    ])) .
                    sb_p('Thank you for completing Workplace Health & Safety. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about spotting and reporting hazards?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
