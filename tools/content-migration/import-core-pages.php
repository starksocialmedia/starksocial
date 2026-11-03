<?php
/**
 * Live-sourced copy for Home, About and Contact (v27.3.0). Text is from the
 * live pages (live-outlines/2.md, 31.md, 91.md). Saves by ACF field KEY.
 *   wp eval-file tools/content-migration/import-core-pages.php
 * The homepage hero is in the template and is not touched.
 */

/* Home ---------------------------------------------------------------- */
$home = (int) get_option( 'page_on_front' );
$h = function ( $n ) { return 'field_stark_home_' . $n; };
update_field( $h( 'process_heading' ), 'A simple system for brands that want consistency without the agency mess.', $home );
update_field( $h( 'process_steps' ), array(
    array( 'title' => 'Clarify the direction', 'description' => 'We align your messaging, visuals, and priorities so everything points the same way—no guessing, no wasted effort.' ),
    array( 'title' => 'Build a simple system', 'description' => 'We define what to update, what to post, and what actually matters right now—so your marketing is intentional, not reactive.' ),
    array( 'title' => 'Keep it moving', 'description' => 'We execute, refine, and adapt as things evolve—so your brand stays consistent and relevant without constant resets.' ),
), $home );
// The FAQ is Phase 2 draft copy (live has no homepage FAQ) — say so on the page.
$faq_heading = (string) get_field( 'faq_heading', $home );
if ( false === strpos( $faq_heading, '[DRAFT' ) ) {
    update_field( $h( 'faq_heading' ), trim( ( $faq_heading ?: 'Questions, answered' ) . ' [DRAFT — not on live]' ), $home );
}
update_field( $h( 'copy_status' ), 'live', $home );

/* About --------------------------------------------------------------- */
$about = get_page_by_path( 'about' )->ID;
$a = function ( $n ) { return 'field_stark_about_' . $n; };
update_field( $a( 'hero_eyebrow' ), 'About Stark Social', $about );
update_field( $a( 'hero_title' ), 'A steady marketing partner for work that matters.', $about );
update_field( $a( 'hero_intro' ), 'Stark Social is a founder-led marketing studio partnering with organizations doing meaningful work — helping them show up with clarity, consistency, and care.', $about );
update_field( $a( 'hero_cta' ), array( 'title' => 'Start a conversation', 'url' => '/contact/', 'target' => '' ), $about );
update_field( $a( 'story_heading' ), 'Because good work deserves to be represented well.', $about );
update_field( $a( 'story_body' ),
    '<p>They’re doing meaningful work. They care deeply about their mission, their people, and the communities they serve. But their brand, website, or digital presence doesn’t fully reflect that reality — or has become fragmented over time.</p>' .
    '<p>Often, the issue isn’t ambition or effort. It’s a lack of cohesion. Too many decisions made in isolation. Too many vendors. Too much noise. And not enough strategic ownership. holding it all together. <strong>[NEEDS REVIEW: “They’re” has no antecedent and “ownership. holding” is a broken sentence — both as on live]</strong></p>' .
    '<p>Stark exists to bring that clarity back. We work closely, think holistically, and stay accountable from strategy through execution — so what you do and how you’re represented are finally aligned.</p>' .
    '<p><strong>A small team working at the level the work actually requires.</strong></p>' .
    '<h3>How we think: marketing works best when it’s built on understanding — not urgency.</h3>' .
    '<p>We believe strong brands are clear about who they’re for, what they stand for, and how they show up. When that foundation is missing, no amount of content, platforms, or tactics can compensate for it.</p>' .
    '<p>Our approach favors fewer decisions made well over constant activity. We focus on alignment first — between message and mission, strategy and execution, expectations and reality.</p>' .
    '<p>We don’t chase attention. We earn trust by being thoughtful, consistent, and intentional — and by building systems that support the work long after launch.</p>' .
    '<p><strong>Marketing should make things simpler, not louder.</strong></p>',
    $about );
update_field( $a( 'team_heading' ), 'Led by the people who do the work.', $about );
update_field( $a( 'team_members' ), array(
    array( 'photo' => 269, 'name' => 'Deanna Miller', 'role' => 'Co-Founder · Strategy & Marketing',
        'bio' => 'Deanna leads brand strategy, messaging, and marketing direction at Stark Social. Her work sits at the intersection of clarity, storytelling, and long-term growth — helping organizations articulate who they are, who they serve, and how they show up. She’s known for asking the right questions early, simplifying what’s become complicated, and helping teams find language that actually sounds like them. Her approach is thoughtful, collaborative, and grounded in real-world execution — not theory.' ),
    array( 'photo' => 463, 'name' => 'Nathan Imhoff', 'role' => 'Co-Founder · Creative & Technical',
        'bio' => 'Nathan leads creative, design, and technical execution at Stark Social. His background bridges design and development, allowing strategy to carry cleanly through into systems that are functional, durable, and considered. He’s deeply involved in how things are built — from structure and usability to the details that shape how a brand is experienced day to day. Nathan is known for translating complex ideas into work that feels simple, intentional, and quietly confident. His focus is on building things that last — technically, visually, and strategically.' ),
), $about );
// Live has no values list; the Phase 2 [SAMPLE] values are removed rather than kept as fake copy.
update_field( $a( 'values' ), array(), $about );
update_field( $a( 'where_body' ), 'Based in Southern California (Los Angeles + Santa Clarita). Supporting businesses nationwide.', $about );
update_field( $a( 'final_cta' ), array(
    'heading' => 'A steady partner for work that matters.',
    'subhead' => 'Stark Social works as a fractional marketing partner to organizations doing meaningful work — helping them show up with clarity, consistency, and care. If you’re looking for a thoughtful, long-term partnership — not a vendor — we’d love to hear from you. [NEEDS REVIEW: “fractional” — Fractional CMO was dropped as a service]',
    'button'  => array( 'title' => "Let's Chat", 'url' => '/contact/', 'target' => '' ),
), $about );

/* Contact ------------------------------------------------------------- */
$contact = get_page_by_path( 'contact' )->ID;
$c = function ( $n ) { return 'field_stark_contact_' . $n; };
update_field( $c( 'hero_title' ), 'Let’s talk.', $contact );
update_field( $c( 'hero_intro' ), 'If you’re exploring a thoughtful, long-term marketing partnership, we’d love to hear more about what you’re working on. No pressure. No pitches. Just a conversation to see if it’s a fit.', $contact );
update_field( $c( 'form_heading' ), 'Tell us a bit about your work.', $contact );
update_field( $c( 'form_intro' ), 'If you’re not sure where to start, that’s okay. Share what you can, and we’ll take it from there. We’re a small, senior-led team. You’ll hear directly from the people doing the work.', $contact );
update_field( $c( 'address' ), "P.O. Box 55081\nValencia, CA 91385-0081", $contact );
update_field( $c( 'phone' ), '+1 (661) 513-3996', $contact );
update_field( $c( 'email' ), 'hello@starksocial.com', $contact );
update_field( $c( 'hours' ), array(
    array( 'days' => 'Mon–Thurs', 'time' => '9:00 AM – 5:00 PM' ),
    array( 'days' => 'Friday', 'time' => '9:00 AM – 4:00 PM' ),
), $contact );

WP_CLI::success( "Home $home, About $about, Contact $contact updated from live copy" );
