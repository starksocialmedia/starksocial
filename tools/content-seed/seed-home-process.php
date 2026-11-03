<?php
// Homepage process steps (v27.1.3). Placeholder copy — the homepage Copy status
// stays Draft, so the review banner shows until Nathan approves. Saves by KEY.
$k  = function ( $n ) { return 'field_stark_home_' . $n; };
$id = (int) get_option( 'page_on_front' );
update_field( $k( 'process_heading' ), 'How we work', $id );
update_field( $k( 'process_steps' ), array(
    array( 'title' => 'Discover', 'description' => 'We learn your business, your customers and what is working today.' ),
    array( 'title' => 'Design',   'description' => 'We set the direction: messaging, look and a plan you sign off on.' ),
    array( 'title' => 'Build',    'description' => 'We make it: the site, the campaigns, the content.' ),
    array( 'title' => 'Grow',     'description' => 'We measure, report and refine every month.' ),
), $id );
WP_CLI::success( "Home ($id) process seeded" );
