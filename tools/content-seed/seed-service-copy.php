<?php
// First-draft service copy (v27.0.13). Saves by ACF field KEY. Based on live /services/* pages.
$k = function ( $n ) { return 'field_stark_svc_' . $n; };
$copy = array(
'social-media-management' => array(
  'title' => 'Social Media Management',
  'card' => 'A planned, consistent social presence that supports real business goals.',
  'thesis' => 'Consistent posting is nice. Consistent strategy is what makes it work. We plan, write, design and schedule your social channels, so you stop guessing what to post and start seeing what it does for the business.',
  'included' => array(
    array( 'megaphone', 'Strategy and content direction', 'Clear goals, audience and voice, so every post has a reason to exist.' ),
    array( 'layout', 'Content calendar and scheduling', 'A month planned ahead, approved in one pass, published on time.' ),
    array( 'pen', 'Copywriting and creative', 'Captions, graphics and short video, written and designed in-house.' ),
    array( 'users', 'Profile optimization', 'Bios, links, highlights and branding that match the rest of your business.' ),
    array( 'chart', 'Reporting and refinement', 'Plain-language reports on what worked, what didn\'t and what we\'re changing.' ),
  ),
  'process' => array(
    array( 'Align on strategy', 'Goals, audience, voice and what success actually means for your business.' ),
    array( 'Plan the calendar', 'A month of content mapped to your offers, seasons and events.' ),
    array( 'Create and schedule', 'We write, design and schedule. Nothing goes live without your approval.' ),
    array( 'Review and refine', 'We watch what resonates, cut what doesn\'t, and adjust next month\'s plan.' ),
  ),
  'faq' => array(
    array( 'How involved do I need to be?', 'Lightly. You approve the monthly calendar and join a short check-in. We handle the planning, writing, design and posting.' ),
    array( 'Do you post without approval?', 'No. Nothing goes live without your sign-off unless we agree otherwise up front.' ),
    array( 'Which platforms do you manage?', 'Usually Facebook, Instagram and LinkedIn, plus Google Business Profile posts. We recommend platforms based on where your customers actually spend time, not on trends.' ),
    array( 'How quickly will we see results?', 'Social is cumulative. Most businesses see steadier engagement within the first few months, with stronger momentum after that.' ),
    array( 'Is there a long-term contract?', 'No lock-ins. We would rather earn the relationship month by month.' ),
  ),
  'cta' => 'Ready to stop scrambling for posts?',
),
'paid-advertising' => array(
  'title' => 'Paid Advertising',
  'card' => 'Google, Meta and LinkedIn campaigns where every dollar has a job.',
  'thesis' => 'Clicks are easy. Qualified leads and sales take strategy. We plan, launch and manage paid campaigns around your business goals, measure every dollar, and only scale what is proven.',
  'included' => array(
    array( 'target', 'Campaign strategy', 'Goals, audiences, offers and budget guardrails, defined before any money is spent.' ),
    array( 'chart', 'Tracking and attribution', 'Conversion tracking set up and verified, so results are real, not estimated.' ),
    array( 'pen', 'Ad creative and copy', 'Multiple angles and hooks built for testing, not one ad and a hope.' ),
    array( 'search', 'Targeting and testing', 'Controlled tests across audiences and messages to find what actually converts.' ),
    array( 'layout', 'Transparent reporting', 'You always know what is running, why, and what it costs.' ),
  ),
  'process' => array(
    array( 'Foundation', 'Tracking, access, success metrics and spend guardrails, confirmed before launch.' ),
    array( 'Build', 'Creative angles and audience hypotheses turned into a structured campaign.' ),
    array( 'Launch', 'Controlled spend and real data. We validate tracking and watch early signals.' ),
    array( 'Optimize and scale', 'Shift budget to what works, cut what doesn\'t, and scale with proof.' ),
  ),
  'faq' => array(
    array( 'How much budget do we need to start?', 'It depends on your goals and competition, not an arbitrary minimum. We recommend a starting range that allows real testing without unnecessary risk.' ),
    array( 'How quickly will we see results?', 'Ads produce data within days. Reliable performance comes from a few weeks of testing and refinement.' ),
    array( 'Do we stay in control of spend?', 'Yes. You always know what is running, why it is running and how much it costs. Nothing scales without your agreement.' ),
    array( 'What if the ads don\'t perform?', 'Underperformance is information. We adjust creative, targeting and offers based on the data instead of guessing.' ),
    array( 'Is there a long-term contract?', 'No lock-ins. We prefer to earn trust through results and transparency.' ),
  ),
  'cta' => 'Ready to put your ad budget to work?',
),
'web-design' => array(
  'title' => 'Web Design',
  'card' => 'Fast, clear WordPress websites built to earn trust and bring in leads.',
  'thesis' => 'Most websites fail quietly: they look fine but leave visitors unsure what you do or what to do next. We design and build fast, accessible WordPress sites with clear structure, so your website does the explaining for you.',
  'included' => array(
    array( 'layout', 'Structure and strategy', 'Page hierarchy and messaging planned around what visitors need to find.' ),
    array( 'pen', 'Custom design', 'A design system built around your brand, not a template.' ),
    array( 'search', 'SEO foundations', 'Clean code, fast load times, schema and on-page basics from day one.' ),
    array( 'users', 'Accessibility built in', 'Readable contrast, keyboard support and clean markup, not bolted on later.' ),
    array( 'shield', 'Launch and care', 'Testing, launch, and optional ongoing updates, backups and monitoring.' ),
  ),
  'process' => array(
    array( 'Discovery and strategy', 'Purpose, audience and priorities defined before design begins.' ),
    array( 'Structure and design', 'Sitemap, wireframes and a design system that supports your content.' ),
    array( 'Build and test', 'Developed in WordPress and tested on every screen size.' ),
    array( 'Launch and refine', 'Go live, watch how people use it, and keep improving.' ),
  ),
  'faq' => array(
    array( 'How long does a website project take?', 'Most projects take 6–10 weeks, depending on scope, feedback timing and content readiness. You get a clear timeline before we start.' ),
    array( 'Do you write the website content?', 'We help shape and refine it, but we don\'t replace your voice. We can work from your existing copy, write new messaging with you, or bring in a copywriter.' ),
    array( 'Will I be able to update the site myself?', 'Yes. We build in WordPress with editing tools that let you change content without breaking the layout.' ),
    array( 'What happens after launch?', 'We don\'t disappear. You get post-launch support, and optional ongoing care for updates, backups and improvements.' ),
    array( 'How involved do I need to be?', 'Focused, but not overwhelming: input on goals, feedback at key points, and approvals. We handle the rest.' ),
  ),
  'cta' => 'Ready to build something that lasts?',
),
'seo' => array(
  'title' => 'SEO',
  'card' => 'Search visibility that compounds, built on technical health and useful pages.',
  'thesis' => 'SEO isn\'t about chasing rankings. It\'s about pages that answer real questions, a site search engines can understand, and a plan someone actually owns. We handle all three, so visibility builds month over month.',
  'included' => array(
    array( 'search', 'Search intent research', 'Keywords grounded in what your customers actually search for, not volume for its own sake.' ),
    array( 'layout', 'On-page optimization', 'Titles, structure and content tuned so each page clearly answers one need.' ),
    array( 'shield', 'Technical SEO', 'Speed, indexing, schema and crawl issues fixed before they cost you.' ),
    array( 'target', 'Local SEO', 'Google Business Profile, local pages and listings for the markets you serve.' ),
    array( 'chart', 'Reporting that explains', 'What moved, why it moved, and what we are doing next, in plain language.' ),
  ),
  'process' => array(
    array( 'Audit and research', 'Search intent, competitors and technical health, reviewed before anything changes.' ),
    array( 'Fix the foundation', 'Technical issues and site structure cleaned up first, so new work can compound.' ),
    array( 'Build and optimize pages', 'Improve the pages you have and create the ones searchers are looking for.' ),
    array( 'Measure and refine', 'Learn from real search data and adjust, without chasing every fluctuation.' ),
  ),
  'faq' => array(
    array( 'How long does SEO take to work?', 'SEO builds over time. Most businesses see early signals within a few months, with stronger momentum as content and authority stack up.' ),
    array( 'Do you guarantee rankings?', 'No, and be wary of anyone who does. We guarantee a clear strategy, measurable progress and full transparency about what is working and why.' ),
    array( 'Do we need to publish blog posts?', 'Not always. Sometimes improving the pages you already have does more. When new content makes sense, we plan it around real questions your customers ask.' ),
    array( 'Will this help our other marketing?', 'Yes. Better pages convert better, which makes paid ads, social and email more effective too.' ),
    array( 'Is there a long-term contract?', 'No lock-ins. SEO works best over time, but we keep clients by delivering, not by contract.' ),
  ),
  'cta' => 'Ready for clarity, not guesswork?',
),
'content-creation' => array(
  'title' => 'Content Creation',
  'card' => 'Photo, video and design that look sharp and perform across channels.',
  'thesis' => 'Content for content\'s sake doesn\'t move anything. We plan and produce photo, short-form video and design for the platforms where your customers spend time, then use performance to decide what to make next.',
  'included' => array(
    array( 'camera', 'Photo and video', 'Images and footage of your team, your work and your space.' ),
    array( 'megaphone', 'Short-form video', 'Vertical video for Reels, TikTok and Shorts, edited for hooks and pacing.' ),
    array( 'target', 'Paid social creative', 'Static and motion ads in multiple variations, built for testing.' ),
    array( 'layout', 'Brand templates', 'Reusable layouts that keep content consistent as volume grows.' ),
    array( 'pen', 'Written content', 'Blog posts, emails and captions written in your voice.' ),
  ),
  'process' => array(
    array( 'Strategy and direction', 'Goals, audiences and what each piece of content needs to do.' ),
    array( 'Production', 'Modular assets across formats, built to test different hooks and messages.' ),
    array( 'Distribution and testing', 'Content deployed across paid, organic and owned channels to generate real signal.' ),
    array( 'Learn and iterate', 'Double down on what works and retire what doesn\'t.' ),
  ),
  'faq' => array(
    array( 'What kind of content do you create?', 'Photo, short-form video, ad creative, design templates and written content. The mix depends on what your audience responds to and what your goals need.' ),
    array( 'How involved do we need to be?', 'Collaborative, but not heavy. We align on direction and you review work at key points. We handle planning, production and editing.' ),
    array( 'Do you post content without approval?', 'No. Nothing goes live without your sign-off unless we agree on a different process up front.' ),
    array( 'How do you measure success?', 'By whether content does its job: engagement that leads somewhere, ads that convert, and a brand that looks consistent everywhere.' ),
    array( 'Is this ongoing or project-based?', 'Both work. Some businesses need a steady content engine; others need a strong library to start. We recommend what fits.' ),
  ),
  'cta' => 'Ready for content with a job to do?',
),
'brand-strategy-identity' => array(
  'title' => 'Brand Strategy',
  'card' => 'Positioning, messaging and identity that make your business easy to trust.',
  'thesis' => 'Most brands don\'t struggle with creativity. They struggle with direction. We define who you are, who you serve and how you show up, then turn it into messaging and a visual identity your whole team can use.',
  'included' => array(
    array( 'target', 'Positioning', 'Who you serve, what makes you different, and why it matters to them.' ),
    array( 'pen', 'Messaging framework', 'Core message, value proposition and voice guidelines your team can write from.' ),
    array( 'layout', 'Visual identity', 'Logo, color, typography and the rules that keep them consistent.' ),
    array( 'shield', 'Brand guide', 'A living guide that keeps your website, social and print on-brand.' ),
    array( 'users', 'Team alignment', 'Working sessions that get decision-makers agreeing before anything is designed.' ),
  ),
  'process' => array(
    array( 'Discovery', 'Conversations with your team, a look at your audience and an honest read on competitors.' ),
    array( 'Positioning and messaging', 'A clear point of view on who you are for and what you say.' ),
    array( 'Identity design', 'Visual identity built to express the strategy, not just look current.' ),
    array( 'Rollout', 'Guidelines, templates and launch support across your channels.' ),
  ),
  'faq' => array(
    array( 'Do we need a full rebrand?', 'Not always. Often clearer messaging does more than a new logo. We will tell you honestly which one you need.' ),
    array( 'What do we get at the end?', 'A brand guide covering positioning, messaging, voice, logo usage, color and typography, plus the source files.' ),
    array( 'How long does a brand project take?', 'It depends on scope. A messaging refresh moves faster than a full identity. You get a clear timeline before we start.' ),
    array( 'How involved do we need to be?', 'Focused, but not overwhelming. We need your input on goals and feedback at key points. We handle research, writing and design.' ),
    array( 'What if we\'re not a fit?', 'We will say so early. Good brand work depends on alignment, and we would rather be honest than force it.' ),
  ),
  'cta' => 'Let\'s see if this is a good fit.',
),
'audit-consulting' => array(
  'title' => 'Audit & Consulting',
  'card' => 'An honest look at your marketing, and a clear plan for what to fix first.',
  'thesis' => 'Before you hire anyone, find out what is actually working. We review your website, search visibility, social and ads, then hand you a prioritized plan in plain language. Run it yourself, or have us do it.',
  'included' => array(
    array( 'search', 'Website and SEO review', 'Speed, structure, technical health and how you show up in search.' ),
    array( 'megaphone', 'Social and content review', 'What you post, how it performs, and whether it supports your goals.' ),
    array( 'target', 'Ads and tracking review', 'Account structure, spend efficiency, and whether conversion tracking actually works.' ),
    array( 'shield', 'Access and ownership check', 'Who has the keys to your accounts, and whether you actually own them.' ),
    array( 'chart', 'Prioritized action plan', 'What to fix first, what can wait, and what to stop paying for.' ),
  ),
  'process' => array(
    array( 'Intake call', 'Your goals, your current setup and the questions you want answered.' ),
    array( 'Review', 'We go through your website, search, social, ads and tracking by hand.' ),
    array( 'Written plan', 'Findings ranked by impact and effort, without jargon.' ),
    array( 'Walkthrough', 'We walk you through the plan, answer questions, and you decide what happens next.' ),
  ),
  'faq' => array(
    array( 'Is this like the free "SEO audit" emails we get?', 'No. Those are sales pitches generated by automated tools. Ours is a manual review by the people who would do the work, with no pressure to sign anything.' ),
    array( 'Do we have to hire you afterward?', 'No. The plan is yours. Some clients run it themselves; some ask us to handle part or all of it.' ),
    array( 'What do you need from us?', 'A short intake call and read-only access to your website, analytics, ad accounts and social profiles.' ),
    array( 'How much does an audit cost?', 'It depends on how many channels we review. You get a fixed quote before we start, with no surprises.' ),
    array( 'How long does it take?', 'Usually a few weeks from intake to walkthrough, depending on how many channels we review.' ),
  ),
  'cta' => 'Find out what is actually working.',
),
);

foreach ( $copy as $slug => $c ) {
    $page = get_page_by_path( $slug );
    $id = $page ? $page->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $c['title'], 'post_name' => $slug ) );
    update_field( $k( 'copy_status' ), 'draft', $id );
    update_field( $k( 'hero_eyebrow' ), 'Services', $id );
    update_field( $k( 'hero_thesis' ), $c['thesis'], $id );
    update_field( $k( 'card_description' ), $c['card'], $id );
    update_field( $k( 'included_heading' ), 'What\'s included', $id );
    update_field( $k( 'included_items' ), array_map( function ( $r ) { return array( 'icon' => $r[0], 'title' => $r[1], 'description' => $r[2] ); }, $c['included'] ), $id );
    update_field( $k( 'process_heading' ), 'How we work', $id );
    update_field( $k( 'process_steps' ), array_map( function ( $r ) { return array( 'title' => $r[0], 'description' => $r[1] ); }, $c['process'] ), $id );
    update_field( $k( 'case_study' ), array( 'client' => '', 'headline' => '', 'result_value' => '', 'result_label' => '', 'summary' => '', 'image' => '', 'link' => '' ), $id );
    update_field( $k( 'faq_heading' ), 'Questions, answered', $id );
    update_field( $k( 'faq_items' ), array_map( function ( $r ) { return array( 'question' => $r[0], 'answer' => $r[1] ); }, $c['faq'] ), $id );
    update_field( $k( 'final_cta' ), array( 'heading' => $c['cta'], 'subhead' => 'No pressure. If we\'re not a fit, we\'ll tell you.', 'button' => '' ), $id );
    WP_CLI::log( "$slug → $id" );
}
