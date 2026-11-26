<?php
// Emotional Intelligence (Skills): built around the existing Articulate Rise lesson (EmotionalIntelligence).
return [
    'shortname' => 'Emotional Intelligence',
    'fullname' => 'Emotional Intelligence',
    'category' => 'skills',
    'mandatory' => false,
    'minutes' => 30,
    'summary' => 'Emotional intelligence is the ability to manage your own emotions and understand and respond to the feelings of others. This course explains what it is, its five elements and practical ways to use it at work, as a worker, a co-worker and a leader.',
    'outcomes' => [
        'Explain what emotions and emotional intelligence are',
        'Describe how emotional intelligence (EQ) differs from IQ',
        'Name the five elements of emotional intelligence',
        'Use practical strategies to improve your emotional intelligence at work',
    ],
    'sections' => [
        [
            'name' => 'Welcome',
            'summary' => 'What this course covers, and how it works.',
            'items' => [
                ['type' => 'page', 'name' => 'Start here', 'content' =>
                    sb_banner('Emotional Intelligence', 'Understand feelings, yours and others’',
                        'Cognitive ability helps you reach career milestones. Managing your emotions affects your performance, your productivity and your relationships at work.') .
                    sb_stats([
                        ['5', 'elements of emotional intelligence'],
                        ['10', 'strategies to work smarter with emotional intelligence'],
                        ['30 min', 'to complete this course, at your own pace'],
                    ]) .
                    sb_keypoints('What you will learn', [
                        'What emotions are, and the basic emotions all cultures share',
                        'What emotional intelligence is, and how EQ compares with IQ',
                        'The main models of emotional intelligence and its five elements',
                        'How to improve emotional intelligence as a worker, a co-worker and a leader',
                    ]) .
                    '<h3>How this course works</h3>' .
                    sb_steps([
                        ['Learn', 'Work through the interactive lesson. It has short knowledge checks along the way.'],
                        ['Review', 'Read the key takeaways page to fix the main ideas.'],
                        ['Reflect', 'Answer a one-question poll about the element you most want to build.'],
                        ['Check', 'Answer six questions. You need 7 out of 10 to pass, and you can try again.'],
                    ]) .
                    sb_note('tip', 'Good news', sb_p('Unlike IQ, emotional intelligence can be learned and improved. Every idea in this course is something you can practise.'))],
            ],
        ],
        [
            'name' => 'Lesson',
            'summary' => 'The full interactive lesson, followed by its key takeaways.',
            'items' => [
                ['type' => 'keep', 'mod' => 'scorm', 'ref' => 'EmotionalIntelligence', 'name' => 'Emotional intelligence at work',
                    'intro' => 'An interactive lesson on emotions, emotional intelligence and its five elements, and how to use it in the workplace.'],
                ['type' => 'page', 'name' => 'Key takeaways', 'content' =>
                    sb_banner('After the lesson', 'EQ can be learned', 'The ideas worth keeping from the lesson, on one page.') .
                    sb_quote('A form of social intelligence that involves the ability to monitor one’s own and others’ feelings and emotions, to discriminate among them, and to use this information to guide one’s thinking and action.',
                        'John D. Mayer and Peter Salovey, who coined the term in 1990') .
                    sb_columns([
                        ['IQ', sb_ul(['Measures the ability to think', 'The capacity to learn and comprehend', 'Linked to success in school', 'A natural ability'])],
                        ['EQ', sb_ul(['Measures emotional intelligence', 'The capacity to recognise, regulate and express your own emotions', 'Linked to success in real life', 'Can be learned and improved'])],
                    ]) .
                    '<h3>The five elements of emotional intelligence</h3>' .
                    sb_steps([
                        ['Self-awareness', 'Knowing how you feel and act, how others see your actions, and your own strengths and weaknesses.'],
                        ['Self-regulation', 'Staying calm, thinking before you speak or react, and taking responsibility for your actions.'],
                        ['Motivation', 'The drive towards your goals: initiative, commitment and perseverance.'],
                        ['Empathy', 'Understanding the emotions and reactions of others. It starts with understanding yourself.'],
                        ['Social awareness', 'Listening well and quickly seeing what matters to the people you speak with.'],
                    ]) .
                    sb_dodont(
                        ['Speak up when there is a problem', 'Offer your help to colleagues', 'Give a compliment before a criticism', 'Ask for help and admit your mistakes'],
                        ['Stereotype the people you work with', 'Take other people’s behaviour personally', 'Make promises you can’t keep', 'Rely on data alone when you decide']) .
                    sb_note('info', 'Why it matters at work', sb_p('Higher job satisfaction and better job performance are the two main reasons emotional intelligence matters at work. A lack of it harms communication and decision-making.'))],
            ],
        ],
        [
            'name' => 'Practice',
            'summary' => 'Think about where you want to grow.',
            'items' => [
                ['type' => 'choice', 'name' => 'Poll: which element will you work on?', 'intro' => 'Pick the element of emotional intelligence you most want to build. Results are shown after you answer.',
                    'options' => ['Self-awareness', 'Self-regulation', 'Motivation', 'Empathy', 'Social awareness']],
            ],
        ],
        [
            'name' => 'Check your knowledge',
            'summary' => 'Six questions. You need 7 out of 10 to pass, and you can try again.',
            'items' => [
                ['type' => 'quiz', 'name' => 'Final check', 'intro' => 'Six questions on the lesson.', 'pass' => 7,
                    'after' => ['Emotional intelligence at work', 'Key takeaways'], 'questions' => [
                    ['mc', 'What are the five elements of emotional intelligence?',
                        'Self-awareness, self-regulation, motivation, empathy and social awareness',
                        ['Self-awareness, self-regulation, motivation, sympathy and social awareness', 'Self-assurance, self-regulation, motivation, empathy and social awareness', 'Self-awareness, self-regulation, intuition, empathy and social awareness'],
                        'These are the five elements described by Daniel Goleman.'],
                    ['mc', 'Which element is the ability to stay calm, think before speaking or reacting, and express yourself appropriately?',
                        'Self-regulation', ['Self-awareness', 'Motivation', 'Social awareness'],
                        'Self-regulation also includes taking responsibility for your actions and adjusting to change.'],
                    ['mc', 'According to Plutchik’s wheel of emotions, love is a combination of which two core emotions?',
                        'Joy and trust', ['Joy and disgust', 'Anticipation and surprise', 'Trust and anticipation'],
                        'Plutchik suggested that combining the eight core emotions creates others. Love combines joy and trust.'],
                    ['tf', 'Unlike IQ, emotional intelligence can be learned and improved.', true,
                        'Nobody can change their cognitive ability, but everyone can learn to manage their emotions.'],
                    ['mc', 'Which of these is NOT a way to improve emotional intelligence as a co-worker?',
                        'Stereotyping the people you work with', ['Not taking things too seriously', 'Offering your help', 'Being ready to draw the line'],
                        'Assumptions about colleagues lead to prejudice and bias.'],
                    ['tf', 'A lack of emotional intelligence at work can harm communication and decision-making.', true,
                        'Low EQ means less understanding of your own and others’ emotions, and less effective communication.'],
                ]],
            ],
        ],
        [
            'name' => 'Wrap-up',
            'summary' => 'What to remember, and your feedback.',
            'items' => [
                ['type' => 'page', 'name' => 'What to remember', 'content' =>
                    sb_banner('Wrap-up', 'Know yourself, then understand others', 'Empathy starts with self-awareness.') .
                    sb_note('summary', 'In summary', sb_ul([
                        'Emotional intelligence is the ability to manage your own emotions and understand and respond to the feelings of others.',
                        'It is a good predictor of success, and unlike IQ it can be learned.',
                        'Its five elements are self-awareness, self-regulation, motivation, empathy and social awareness.',
                        'At work it brings higher job satisfaction and better performance.',
                    ])) .
                    sb_keypoints('Try this week', [
                        'Listen with compassion, without planning your reply while the other person talks',
                        'Make it safe for people to share how they feel',
                        'Speak up early about something that matters to you',
                        'Give generously when a point means more to someone else than to you',
                    ]) .
                    sb_p('Thank you for completing Emotional Intelligence.')],
                ['type' => 'feedback', 'name' => 'Rate this course', 'intro' => 'Two minutes, anonymous. It helps us improve the course.', 'after' => ['Final check'],
                    'thanks' => 'Thank you. Your feedback goes to the learning team.',
                    'questions' => [
                        ['choice', 'How useful was this course for your daily work?', ['Very useful', 'Useful', 'Somewhat useful', 'Not useful']],
                        ['choice', 'How confident are you now about managing emotions at work?', ['Very confident', 'Confident', 'A little unsure', 'Not confident']],
                        ['text', 'What one thing would you change about this course?'],
                    ]],
            ],
        ],
    ],
];
