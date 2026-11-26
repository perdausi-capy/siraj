<?php
// Giving Feedback That Helps: the SBI model, specific praise and receiving feedback well (Leadership & Management).
return [
    'shortname' => 'FBK301',
    'fullname' => 'Giving Feedback That Helps',
    'category' => 'leadership',
    'mandatory' => false,
    'minutes' => 35,
    'cover' => 'img/photo/lead.jpg',
    'summary' => 'Good feedback helps people grow. Poor feedback, or none at all, leaves them guessing. In 35 minutes you will learn a simple model for giving feedback that is clear, fair and useful, and how to receive it well yourself.',
    'outcomes' => [
        'Give feedback using the Situation, Behaviour, Impact (SBI) model',
        'Give praise that is specific and meaningful',
        'Raise a performance problem calmly and clearly',
        'Choose the right time and place for feedback',
        'Receive feedback without becoming defensive',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'Why feedback matters, and how this course works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Giving Feedback That Helps', 'Feedback is a gift, if it is given well',
                        'Most people want to know how they are doing. The problem is rarely the message. It is how, when and where it is said.') .
                    sb_stats([
                        ['35 min', 'to complete this course, at your own pace'],
                        ['3 parts', 'in the SBI model: Situation, Behaviour, Impact'],
                        ['7 out of 10', 'to pass the final check, with as many tries as you need'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'A simple three-part model for any piece of feedback',
                        'How to make praise specific, so it means something',
                        'How to raise a performance problem without damaging the relationship',
                        'When and where to give feedback',
                        'How to listen to feedback about yourself and use it',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Three short lessons with real workplace examples.'],
                        ['Practise', 'Build SBI sentences, vote in a poll and write a real piece of feedback.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                        ['Finish', 'Tell us what you thought and download your certificate.'],
                    ]) .
                    sb_note('info', 'Not just for managers', sb_p('Everyone gives feedback: to colleagues, to suppliers and to their own manager. This course works for all of them.'))],
                ['type' => 'h5p', 'name' => 'Warm-up: which feedback helps most?', 'package' => sb_h5p('multiple-choice-2-6', 'Which feedback helps most', [
                    'question' => '<p>Fatima gave a presentation to a client this morning. Which comment from her manager is the most helpful?</p>',
                    'answers' => [
                        ['correct' => true, 'text' => '<div>“In this morning’s client meeting, you answered the pricing question with clear figures. The client said it gave them confidence to go ahead.”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Yes. It names the moment, the behaviour and the impact, so Fatima knows exactly what to repeat.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>“Great job today!”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Kind, but vague. Fatima doesn’t know what she did well.</div>', 'notChosenFeedback' => '']],
                        ['correct' => false, 'text' => '<div>“You’re a natural presenter.”</div>',
                            'tipsAndFeedback' => ['tip' => '', 'chosenFeedback' => '<div>Praising a trait, not an action, gives her nothing specific to build on.</div>', 'notChosenFeedback' => '']],
                    ],
                    'behaviour' => ['singlePoint' => true, 'randomAnswers' => true, 'enableRetry' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 1 · The SBI model',
            'summary' => 'A simple structure for any feedback, positive or negative.',
            'items' => [
                ['type' => 'book', 'name' => 'Feedback with SBI', 'chapters' => [
                    ['Why feedback goes wrong', sb_banner('Lesson 1', 'Vague feedback leaves people guessing', 'And people who are guessing tend to guess the worst.') .
                        sb_p('Most feedback fails for one of three reasons. It is too vague (“be more proactive”). It judges the person rather than the action (“you’re careless”). Or it comes so late that nobody remembers what happened.',
                            'The fix is to describe what you saw and what it caused, as soon as you reasonably can.') .
                        sb_quote('We all need people who will give us feedback. That’s how we improve.', 'Bill Gates, business leader')],
                    ['Situation, Behaviour, Impact', sb_p('SBI gives you three short parts. Together they make feedback specific, factual and fair.') .
                        sb_steps([
                            ['Situation', 'Say when and where. “In Tuesday’s team meeting…”'],
                            ['Behaviour', 'Describe what the person did or said, as a camera would see it. “…you interrupted Priya three times while she was presenting…”'],
                            ['Impact', 'Explain the effect on you, the team or the work. “…and she didn’t get to finish her proposal, so we couldn’t decide.”'],
                        ]) .
                        sb_note('tip', 'Then ask a question', sb_p('After the impact, invite a reply: “How did it look from your side?” Feedback works best as a conversation, not a speech.')) .
                        sb_dodont(
                            ['Describe actions you saw or heard', 'Use “I noticed” and “the effect was”', 'Keep it to one topic'],
                            ['Use labels like “lazy” or “rude”', 'Say “always” or “never”', 'Pass on gossip as fact'])],
                    ['Praise that is specific', sb_p('Positive feedback deserves the same care. “Well done” is pleasant, but it doesn’t tell anyone what to keep doing. Specific praise does.') .
                        sb_columns([
                            ['Vague praise', sb_p('“Thanks for your help this week.”')],
                            ['Specific praise (SBI)', sb_p('“On Thursday you stayed to finish the supplier comparison. Because of that, we sent the proposal a day early and won the contract.”')],
                        ]) .
                        sb_keypoints('Make praise count', [
                            'Give it soon, while the moment is fresh',
                            'Name the action, not just the person',
                            'Say why it mattered',
                            'Give it in public only if the person is comfortable with that',
                        ])],
                ]],
                ['type' => 'h5p', 'name' => 'Practice: build an SBI sentence', 'package' => sb_h5p('filltheblanks', 'Build an SBI sentence', [
                    'text' => '<p>Fill in the missing words from this lesson.</p>',
                    'questions' => ['<p>SBI stands for Situation, *Behaviour*, Impact.</p><p>The first part, the *situation*, says when and where it happened.</p><p>Describe behaviour the way a *camera* would see it.</p><p>The last part explains the *impact* on the person, team or work.</p>'],
                    'behaviour' => ['caseSensitive' => false, 'acceptSpellingErrors' => true],
                ])],
            ],
        ],
        [
            'name' => 'Lesson 2 · When performance slips',
            'summary' => 'Raising a problem clearly, and choosing the right moment.',
            'items' => [
                ['type' => 'page', 'name' => 'Feedback on performance problems', 'content' =>
                    sb_banner('Lesson 2', 'Clear is kind', 'Hinting at a problem feels gentler, but it leaves the person unable to fix it.') .
                    sb_p('When work is late, below standard or causing problems for others, the person deserves to know, clearly and early. Use SBI, then move to what happens next.') .
                    '<h3>A conversation in five steps</h3>' .
                    sb_steps([
                        ['Open with the purpose', '“I’d like to talk about the weekly sales report.”'],
                        ['Give the SBI', '“The last two reports arrived on Wednesday instead of Monday. The regional team had to plan without the figures.”'],
                        ['Ask and listen', '“What’s been happening?” Listen to the full answer before you reply.'],
                        ['Agree the change', 'Decide together what will be different, and what support is needed.'],
                        ['Follow up', 'Set a date to check in, and give positive feedback when things improve.'],
                    ]) .
                    sb_scenario('Omar’s customer emails have become short and sometimes abrupt. Two customers have complained this month. He is usually one of the strongest people in the team.',
                        'Write the SBI you would use to open the conversation. (One example: “In two customer emails this month, replies were one line with no greeting. Both customers complained that they felt brushed off. What’s been going on?”)') .
                    sb_note('warn', 'Avoid the “feedback sandwich”', sb_p('Wrapping criticism between two compliments makes people distrust your praise and often hides the real message. Be kind, but be direct.'))],
                ['type' => 'page', 'name' => 'Timing and setting', 'content' =>
                    sb_banner('Lesson 2', 'When and where matter as much as what', 'The same words can help or hurt depending on the moment.') .
                    sb_columns([
                        ['Good moments', sb_ul(['Soon after the event, once emotions have settled', 'In a regular one-to-one', 'When you both have time to talk'])],
                        ['Poor moments', sb_ul(['In front of colleagues or customers', 'Just before a big meeting or deadline', 'When either of you is angry'])],
                    ]) .
                    sb_keypoints('Choosing the setting', [
                        'Give corrective feedback in private, always',
                        'Prefer a face-to-face or video conversation over chat or email for anything difficult',
                        'Think about culture and language: some colleagues find direct feedback in a group very uncomfortable',
                        'Keep notes of what you agreed, especially for repeated issues',
                    ]) .
                    sb_faq([
                        ['Can I give feedback by email?', 'Short positive feedback, yes. For anything corrective, talk first, then confirm what you agreed in writing.'],
                        ['How soon is “soon”?', 'Usually within a day or two. Long enough to calm down and get the facts, short enough that everyone remembers.'],
                        ['What if the person gets upset?', 'Pause. Acknowledge it: “I can see this is hard to hear.” Offer to continue later, but do not drop the topic.'],
                    ])],
            ],
        ],
        [
            'name' => 'Lesson 3 · Receiving feedback well',
            'summary' => 'Listening to feedback about yourself, and using it.',
            'items' => [
                ['type' => 'page', 'name' => 'Receiving feedback well', 'content' =>
                    sb_banner('Lesson 3', 'Listen first, decide later', 'You don’t have to agree with feedback to learn something from it.') .
                    sb_p('Hearing criticism is uncomfortable for everyone. The natural reaction is to explain, justify or argue. But the person who gives you feedback has taken a risk. How you respond decides whether they will ever do it again.') .
                    sb_steps([
                        ['Breathe and listen', 'Let them finish. Don’t plan your answer while they are talking.'],
                        ['Ask for an example', '“Can you give me an example, so I understand?”'],
                        ['Say thank you', 'Even if you disagree. Thank them for telling you.'],
                        ['Take time to reflect', 'Decide later what is true and what you will change.'],
                        ['Close the loop', 'Tell them what you did with it. It encourages more honest feedback.'],
                    ]) .
                    sb_dodont(
                        ['Ask questions to understand', 'Look for the part that is true', 'Ask for feedback regularly, not only at reviews'],
                        ['Interrupt or defend straight away', 'Attack the person giving it', 'Ignore it because of how it was said']) .
                    sb_quote('Feedback is the breakfast of champions.', 'Ken Blanchard, management writer')],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Share your experience and write a real piece of feedback.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: what holds you back?', 'intro' => 'What most often stops you giving feedback? Results are shown after you answer.',
                    'options' => ['Worry about upsetting the person', 'Not finding the right words', 'Not having the time', 'Not sure it is my place']],
                ['type' => 'forum', 'name' => 'The best feedback I ever got', 'intro' => 'Share a piece of feedback that helped you, and what made it useful. Leave out names.',
                    'posts' => [['Welcome: feedback that changed something', 'Tell us about feedback that made a real difference to you. What was said, and why did it work? Your story can help someone else give better feedback.']]],
                ['type' => 'assign', 'name' => 'Write an SBI feedback message', 'dueindays' => 21, 'words' => 200,
                    'intro' => 'Practise SBI on a real situation from your work.',
                    'instructions' => sb_p('Think of a recent moment when a colleague did something well, or something that caused a problem. Write the feedback you would give using SBI, in 200 words or fewer. Do not use real names.') .
                        sb_keypoints('Include', ['The situation: when and where', 'The behaviour: what you saw or heard', 'The impact: the effect on you, the team or the work', 'The question you would ask next, or what you would like to see continue'])],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on everything in this course.', 'pass' => 7,
                    'after' => ['Feedback with SBI', 'Feedback on performance problems', 'Timing and setting', 'Receiving feedback well'], 'questions' => [
                    ['mc', 'What does SBI stand for?',
                        'Situation, Behaviour, Impact', ['Summary, Background, Improvement', 'Strengths, Barriers, Ideas', 'Situation, Blame, Instruction'],
                        'SBI describes when and where, what the person did, and what effect it had.'],
                    ['mc', 'Which of these describes behaviour, rather than judging the person?',
                        '“You arrived 20 minutes after the meeting started.”', ['“You’re always late.”', '“You don’t care about this team.”', '“You’re unreliable.”'],
                        'Behaviour is what a camera would see. Labels and “always” make people defensive.'],
                    ['tf', 'Corrective feedback is most effective when given in front of the team, so everyone learns from it.', false,
                        'Corrective feedback should always be given in private. Public criticism causes embarrassment and defensiveness.'],
                    ['mc', 'Which praise is the most useful?',
                        '“Your summary in today’s client call helped them decide quickly. Thank you.”', ['“Great job, as always.”', '“You’re so talented.”', '“Thanks for everything.”'],
                        'Specific praise names the moment, the action and the impact, so the person knows what to repeat.'],
                    ['mc', 'A colleague gives you feedback you disagree with. What is the best first response?',
                        'Ask for an example and thank them', ['Explain straight away why they are wrong', 'Change the subject', 'Point out a mistake they made'],
                        'Listening and asking for an example helps you understand. You can decide later what to do with it.'],
                    ['tf', 'The “feedback sandwich” (praise, criticism, praise) is the best way to raise a problem.', false,
                        'It often hides the real message and makes people distrust your praise. Be kind, but be direct.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Specific, timely, kind', 'Three words that sum up feedback that helps.') .
                    sb_note('summary', 'In summary', sb_ul([
                        '<strong>Use SBI:</strong> say the situation, describe the behaviour, explain the impact.',
                        '<strong>Praise specifically,</strong> so people know what to keep doing.',
                        '<strong>Raise problems early,</strong> in private, and agree a clear next step.',
                        '<strong>Receive feedback well:</strong> listen, ask, thank, then decide.',
                    ])) .
                    sb_note('tip', 'Try it this week', sb_p('Give one piece of specific, SBI-style praise to a colleague before Friday. Notice how they respond.')) .
                    sb_p('Thank you for completing Giving Feedback That Helps. Your certificate appears on the course page and in Achievements once every step is done.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. And yes, we will use the SBI model on what you tell us.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about giving difficult feedback?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
