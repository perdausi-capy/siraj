Design a complete, modern UI/UX for "Siraj Learning", a corporate learning platform (LMS) for companies in the Gulf region (UAE, Saudi, Kuwait, Oman). It must NOT look or feel like a traditional LMS (Moodle, Canvas, Blackboard, TalentLMS). No dashboards made of boxed "blocks", no generic course-card grids with a % bar, no breadcrumb-heavy pages, no admin-looking tables for learners. Think of it as a consumer-grade product that happens to be for work training: the polish of Linear, Arc and Raycast, the motivation of Duolingo and Apple Fitness, the calm reading experience of Notion and Medium, the content browsing of Netflix and Apple TV+ (but professional, not playful).

## Who uses it
- Learners (90% of users): employees doing mandatory compliance training (health & safety, code of conduct, information security) and skills courses. Often on a phone, between tasks. They want to know: what must I do, by when, how long it takes, and then get it done fast.
- Line managers: see their team's progress and who is overdue, nudge people.
- Client admins (HR/L&D): assign training, track completion, export reports.

## Brand
- Name: Siraj Learning ("siraj" means lamp / light in Arabic). Tagline idea: "Learning that lights the way."
- Logo: wordmark "SIRAJ / LEARNING" in thin geometric caps, the "I" replaced by a flame shape with a gradient from mint green #00E69A through cyan #33CCFF to blue #3A70B8. Navy #0F3F66 for the wordmark.
- Use light as the visual metaphor: glow, illumination, soft light gradients, things "lighting up" when completed. Subtle, premium, not neon.
- Palette: deep navy #0F3F66 and near-black navy #0A1119 as anchors, the flame gradient as the signature accent used sparingly (progress, primary actions, achievements), plenty of neutral space. Must have both light and dark mode, designed together.
- Typography: a modern variable sans with character for headings (e.g. Plus Jakarta Sans, Satoshi, General Sans or Geist) and a highly readable sans for UI and reading (e.g. Inter or Geist). Arabic: pair with IBM Plex Sans Arabic or Noto Kufi Arabic.
- Icons: one consistent custom-feeling outline set, 1.5px stroke, rounded joins (Lucide / Phosphor style), with a few duotone "hero" icons using the flame gradient for key moments (streaks, certificates, AI). No emoji-style or clip-art icons.
- Imagery: courses use bold abstract or photographic covers with a consistent treatment (duotone or gradient overlay in brand colours) so the library looks curated, not random stock photos.
- Motion: purposeful micro-interactions (progress filling with a glow, a completion "light up" moment, smooth page transitions, skeleton loading). Respect reduced-motion.

## UX principles
1. One clear next action on every screen. The home screen answers "what should I do now?" in under 3 seconds.
2. Time-aware: always show how long something takes ("6 min") and when it's due, in plain language ("Due Friday").
3. Learning as a focused, distraction-free player, not a page of links. Lessons, videos, PDFs, quizzes all open in one consistent full-screen "learning mode" with a slim progress rail and Next/Previous.
4. Motivation without childishness: streaks, weekly goal ring, certificates as collectible achievements, team leaderboard optional (off by default for compliance).
5. AI built in, not bolted on: an AI tutor available everywhere (command palette + side panel), context-aware ("explain this section", "quiz me", "summarise in Arabic").
6. Global command palette (Ctrl/Cmd+K) to search courses, lessons, people and actions.
7. Fully bilingual English/Arabic with proper RTL mirroring. Dates as DD.MM.YYYY.
8. Mobile-first for learners (bottom tab bar on phone), desktop-first for managers and admins.
9. Accessible: WCAG 2.2 AA contrast, visible focus states, keyboard navigable, 44px touch targets.

## Screens to design (desktop 1440px and mobile 390px, light and dark)
1. Sign in: memorable, branded, with SSO (Microsoft, Google) and a "magic link" option; language switch.
2. Home (learner): personal greeting, the single most important next step as a hero, "Due soon" strip, "Continue" row, weekly goal / streak, recent achievements, AI suggestion. Should feel like an app home, not a dashboard of widgets.
3. My learning: mandatory vs optional, filters (in progress, due soon, completed), clear status and time left. Consider a timeline / agenda view as an alternative to a grid.
4. Explore / catalogue: browse like a streaming app (featured collection hero, rows by topic, "because you completed X", search with instant results).
5. Course overview: cover, why it matters, outcomes, duration, due date, certificate preview, the learning path as a visual journey (steps that light up as completed), instructor, "Start" / "Continue".
6. Learning player: full-screen focus mode for a lesson (reading or video), slim progress rail, notes, AI tutor side panel, Next button, completion moment.
7. Quiz: one question per screen, large tap targets, progress dots, instant feedback, results screen with what to review.
8. Achievements / certificates: wallet-style collection, share/download, expiry and renewal reminders.
9. Notifications / inbox: grouped, actionable (start, snooze, mark done).
10. Manager view: my team at a glance (on track / at risk / overdue), drill into a person, one-click reminder.
11. Admin (HR/L&D): assign training to groups with rules, completion report with charts and export (Excel/CSV/PDF), simple and calm, not a wall of settings.
12. Empty, loading, error and offline states for the key screens.

## Content to use (dummy data, no real companies)
Learner "Layla Hassan", Engineering, Dubai. Courses: Workplace Health & Safety (80%, quiz left, due 14.11.2026), Information Security Essentials (40%, due in 4 days), Fire Safety & Emergency Response (not started), Project Management Fundamentals (20%), Code of Conduct & Ethics (completed, certificate earned 02.11.2026), Leadership for Line Managers (optional). Manager "Omar Rahman" with a team of 12.

## Deliverables
- A small design system first: colour tokens (light/dark), type scale, spacing, radii, elevation, icon set, buttons, inputs, chips, progress (bar, ring, path), cards, tabs, navigation (desktop sidebar or top nav, mobile bottom bar), toasts, modals, command palette.
- Then the screens above, consistent with that system.
- Short notes on key interactions and transitions.
- Output as high-fidelity screens (and, if you can, working HTML/CSS or React + Tailwind so it can be reused).

Technical note for feasibility: the backend is an existing LMS with standard data (courses, sections, activities, completion, due dates, grades, certificates, users, groups). Every screen should be buildable from that data; avoid features that need data an LMS wouldn't have.
