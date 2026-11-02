<?php
/**
 * Episode card — cover art with play badge, episode number, title,
 * duration + date, excerpt. $args: WP_Post or ID.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$ep = stark_podcast_episode( ! empty( $args ) ? $args : null );
?>
<article class="stark-episode-card">
    <div class="stark-episode-card__cover">
        <?php echo stark_attachment_img( $ep['cover_id'], 'medium_large', array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper ?>
        <span class="stark-episode-card__play" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5Z"/></svg></span>
    </div>
    <div class="stark-episode-card__body">
        <?php if ( $ep['number'] ) : ?>
            <p class="stark-episode-card__number">Episode <?php echo esc_html( $ep['number'] ); ?></p>
        <?php endif; ?>
        <h3 class="stark-episode-card__title"><a href="<?php echo esc_url( $ep['url'] ); ?>"><?php echo esc_html( $ep['title'] ); ?></a></h3>
        <p class="stark-episode-card__meta">
            <?php if ( $ep['duration'] ) : ?><span><?php echo esc_html( $ep['duration'] ); ?></span><span aria-hidden="true">·</span><?php endif; ?>
            <time datetime="<?php echo esc_attr( get_the_date( 'c', $ep['id'] ) ); ?>"><?php echo esc_html( get_the_date( '', $ep['id'] ) ); ?></time>
        </p>
        <?php if ( $ep['excerpt'] ) : ?>
            <p class="stark-episode-card__excerpt"><?php echo esc_html( $ep['excerpt'] ); ?></p>
        <?php endif; ?>
    </div>
</article>
