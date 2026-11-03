<?php
// v27.4.1: episode 613 ("Has Snapchat Jumped The Shark?") links /uploads/jumping-the-shark.gif,
// a 404 on live; the file is in /uploads/2024/07/. Run with: wp eval-file <this file>
$post = get_post( 613 );
if ( ! $post ) { WP_CLI::warning( 'Post 613 not found' ); return; }
$fixed = preg_replace( '#(/wp-content/uploads/)(jumping-the-shark\.gif)#', '${1}2024/07/$2', $post->post_content, -1, $n );
$fixed = str_replace( 'https://starksocial.com/wp-content/uploads/2024/07/jumping-the-shark.gif', '/wp-content/uploads/2024/07/jumping-the-shark.gif', $fixed );
if ( $n ) { wp_update_post( array( 'ID' => 613, 'post_content' => $fixed ) ); }
WP_CLI::success( "Fixed $n reference(s)" );
