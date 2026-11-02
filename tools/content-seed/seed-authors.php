<?php
// Author profiles from live's team pages + author meta (real copy). Saves ACF by KEY.
$k = function ( $n ) { return 'field_stark_author_' . $n; };
$people = array(
    1 => array(
        'nicename' => 'nateimhoff', 'display' => 'Nathan Imhoff', 'first' => 'Nathan', 'last' => 'Imhoff', 'photo' => 463,
        'role' => 'Co-Founder · Creative & Technical',
        'highlights' => '40 Under Forty · Award-winning creative work · Built for longevity',
        'instagram' => 'https://instagram.com/n8i.run',
        'bio' => "Nathan Imhoff is a co-founder of Stark Social Media Agency and leads creative direction across web, brand, and visual systems. His work focuses on clarity, structure, and execution—turning complex ideas into clean, usable design that holds up over time.\n\nHis commitment to quality work and community involvement has been recognized with the LA County Sheriff’s Department Star Scroll, and he was selected as one of Santa Clarita’s 40 Under Forty for his dedication to excellence and service.",
        'short' => 'Leads creative, design and technical execution. Turns complex ideas into clean, usable design that holds up over time.',
    ),
    3 => array(
        'nicename' => 'deannaleigh', 'display' => 'Deanna L. Miller', 'first' => 'Deanna', 'last' => 'Miller', 'photo' => 269,
        'role' => 'Co-Founder · Strategy & Marketing',
        'highlights' => '40 Under Forty · 20+ Years Marketing · London Marathon 2024',
        'instagram' => '',
        'bio' => "Deanna L. Miller is a co-founder of Stark Social Media Agency and leads strategy across social, content, and digital platforms. Her work focuses on clarity, structure, and building systems that scale — helping organizations communicate with intention instead of noise.\n\nEarly in her career, Deanna worked in global sourcing and brand management for high-profile video game clients including Ubisoft and Disney Interactive. Over more than 20 years in marketing, she has worked with startups, nonprofits, and established brands, and she was selected as one of Santa Clarita’s 40 Under Forty.",
        'short' => 'Leads strategy across social, content and digital. Brings structure to messaging so brands show up consistently.',
    ),
);
foreach ( $people as $id => $p ) {
    $r = wp_update_user( array( 'ID' => $id, 'user_nicename' => $p['nicename'], 'display_name' => $p['display'], 'first_name' => $p['first'], 'last_name' => $p['last'], 'nickname' => $p['first'], 'description' => $p['bio'] ) );
    if ( is_wp_error( $r ) ) { WP_CLI::warning( $r->get_error_message() ); continue; }
    update_field( $k( 'stark_role' ), $p['role'], 'user_' . $id );
    update_field( $k( 'stark_highlights' ), $p['highlights'], 'user_' . $id );
    update_field( $k( 'stark_photo' ), $p['photo'], 'user_' . $id );
    update_field( $k( 'stark_since' ), 2012, 'user_' . $id );
    update_field( $k( 'stark_podcast_host' ), 1, 'user_' . $id );
    if ( $p['instagram'] ) update_field( $k( 'stark_instagram' ), $p['instagram'], 'user_' . $id );
    WP_CLI::log( "{$p['display']} → " . get_author_posts_url( $id ) );
}
wp_update_user( array( 'ID' => 2, 'display_name' => 'Stark Social Media Agency', 'nickname' => 'Stark Social Media Agency' ) );

// About page team cards: real photos + short bios (names/roles already real).
$about = get_page_by_path( 'about' );
if ( $about ) {
    update_field( 'field_stark_about_team_members', array(
        array( 'photo' => 269, 'name' => 'Deanna L. Miller', 'role' => $people[3]['role'], 'bio' => $people[3]['short'] ),
        array( 'photo' => 463, 'name' => 'Nathan Imhoff', 'role' => $people[1]['role'], 'bio' => $people[1]['short'] ),
    ), $about->ID );
    WP_CLI::log( 'About team updated' );
}
