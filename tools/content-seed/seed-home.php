<?php
// Homepage social proof + FAQ (v27.0.20). Stats are facts from live copy only. Saves by KEY.
$k = function ( $n ) { return 'field_stark_home_' . $n; };
$id = (int) get_option( 'page_on_front' );
update_field( $k( 'copy_status' ), 'draft', $id );
update_field( $k( 'stats' ), array(
    array( 'value' => '14', 'label' => 'Years in business (since 2012)' ),
    array( 'value' => '20+', 'label' => 'Years of marketing experience' ),
    array( 'value' => 'Winner', 'label' => 'Communicator Award for web design' ),
), $id );
update_field( $k( 'faq_heading' ), 'Questions, answered', $id );
update_field( $k( 'faq_items' ), array(
    array( 'question' => 'What kinds of businesses do you work with?', 'answer' => 'Small and mid-sized businesses, nonprofits and service companies across Southern California, plus clients nationwide. If you are doing good work and want your marketing to show it, we should talk.' ),
    array( 'question' => 'Who will we actually work with?', 'answer' => 'The founders. Deanna leads strategy and messaging; Nathan leads creative and technical execution. No account managers, no hand-offs.' ),
    array( 'question' => 'How much does it cost?', 'answer' => 'It depends on scope. After a short call you get a clear quote, so you know what you are paying before any work starts.' ),
    array( 'question' => 'How long does a project take?', 'answer' => 'Most websites take 6–10 weeks. Social, SEO and ads are ongoing, starting with a few weeks of setup. You get a timeline before we begin.' ),
    array( 'question' => 'What does your process look like?', 'answer' => 'Clarify the direction, build a simple system, keep it moving. We align on goals and messaging first, then execute and refine as we learn what works.' ),
    array( 'question' => 'Will it sound like us?', 'answer' => 'Yes. We learn your voice before we write a word, and nothing goes live without your approval.' ),
    array( 'question' => 'Do we have to sign a long-term contract?', 'answer' => 'No lock-ins. We would rather earn the relationship month by month.' ),
    array( 'question' => 'What if we are not a good fit?', 'answer' => 'We will tell you. We would rather be honest early than force a project that will not work.' ),
), $id );
WP_CLI::success( "Home ($id) seeded" );
