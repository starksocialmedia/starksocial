<?php
// Podlove episode data (from live) → post meta on podcast posts, for sites without Podlove.
$d = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
$n = 0;
foreach ( $d['episodes'] as $live_id => $e ) {
    $post = get_post( (int) $live_id );
    if ( ! $post || 'podcast' !== $post->post_type ) { WP_CLI::log( "skip $live_id (not imported)" ); continue; }
    $secs = (int) round( (float) $e['duration'] );
    update_post_meta( $post->ID, 'episode_number', $e['number'] );
    update_post_meta( $post->ID, 'episode_duration', $secs >= 3600 ? gmdate( 'G:i:s', $secs ) : ltrim( gmdate( 'i:s', $secs ), '0' ) );
    update_post_meta( $post->ID, 'episode_duration_seconds', $secs );
    update_post_meta( $post->ID, 'episode_subtitle', $e['subtitle'] );
    if ( ! empty( $e['files']['MP3 Audio'] ) ) {
        update_post_meta( $post->ID, 'episode_audio_url', esc_url_raw( $e['files']['MP3 Audio']['url'] ) );
        update_post_meta( $post->ID, 'episode_audio_size', (int) $e['files']['MP3 Audio']['size'] );
    }
    if ( ! empty( $e['transcript_text'] ) ) update_post_meta( $post->ID, 'episode_transcript', wp_slash( $e['transcript_text'] ) );
    delete_transient( 'stark_podmeta_' . $post->ID );
    $n++;
}
WP_CLI::success( "Updated $n episodes." );
