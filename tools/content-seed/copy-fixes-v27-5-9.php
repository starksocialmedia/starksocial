<?php
/**
 * v27.5.9 copy fixes: syntax and copy-paste errors only, meaning kept. Each fix is an exact
 * string replacement, so it does nothing if Nathan has already edited the field. Idempotent.
 * Usage: wp eval-file copy-fixes-v27-5-9.php
 */
$fixes = array(
    // Content Creation: final CTA heading was Paid Advertising's (copied over on live).
    array( 'content-creation', 'final_cta_heading',
        '[NEEDS REVIEW — same heading as Paid Advertising on live] Ready to turn paid social into a system?',
        'Ready to turn your content into a system?' ),
    array( 'content-creation', 'process_eyebrow', 'How it fit together', 'How it fits together' ),
    // Homepage + services hub card: plural subject.
    array( 'content-creation', 'card_description',
        'Photo and video that looks sharp and performs across channels.',
        'Photo and video that look sharp and perform across channels.' ),
    // About story: "They're" had no antecedent; "ownership. holding" was a broken sentence.
    array( 'about', 'story_body', '<p>They’re doing meaningful work.', '<p>The organizations we work with are doing meaningful work.' ),
    array( 'about', 'story_body',
        'And not enough strategic ownership. holding it all together. <strong>[NEEDS REVIEW: “They’re” has no antecedent and “ownership. holding” is a broken sentence — both as on live]</strong>',
        'And not enough strategic ownership holding it all together.' ),
);
foreach ( $fixes as list( $slug, $key, $from, $to ) ) {
    $page = get_page_by_path( $slug );
    if ( ! $page ) { WP_CLI::warning( "no page $slug" ); continue; }
    $value = (string) get_post_meta( $page->ID, $key, true );
    if ( false === strpos( $value, $from ) ) { WP_CLI::log( "skip $slug.$key (already changed)" ); continue; }
    update_post_meta( $page->ID, $key, str_replace( $from, $to, $value ) );
    WP_CLI::log( "fixed $slug.$key" );
}
WP_CLI::success( 'Copy fixes applied' );
