<?php
/**
 * Author profiles from the live team pages (/about/nathan-imhoff/, /about/deanna-l-miller/),
 * v27.3.21. Full "Background" as the bio (first paragraph = hero), highlights, role and the
 * "How X works" focus areas. Text verbatim from live (author-specs.json, build in the README).
 *   STARK_JSON=tools/content-migration/author-specs.json wp eval-file tools/content-migration/import-authors.php
 */
$specs = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
foreach ( $specs as $login => $a ) {
    $user = get_user_by( 'slug', $login ) ?: get_user_by( 'login', $login );
    if ( ! $user ) { WP_CLI::warning( "no user $login" ); continue; }
    wp_update_user( array( 'ID' => $user->ID, 'description' => $a['description'] ) );
    $acf = 'user_' . $user->ID;
    update_field( 'field_stark_author_stark_role', $a['role'], $acf );
    update_field( 'field_stark_author_stark_highlights', $a['highlights'], $acf );
    update_field( 'field_stark_author_stark_focus_heading', $a['focus_heading'], $acf );
    update_field( 'field_stark_author_stark_focus_intro', $a['focus_intro'], $acf );
    update_field( 'field_stark_author_stark_focus', $a['focus'], $acf );
    WP_CLI::log( "$login ({$user->ID}) updated" );
}
WP_CLI::success( 'Author profiles imported from live' );
