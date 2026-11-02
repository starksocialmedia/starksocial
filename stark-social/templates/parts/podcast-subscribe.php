<?php
/**
 * Subscribe / listen links. $args['variant']: 'light' on dark backgrounds.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$variant = ! empty( $args['variant'] ) ? $args['variant'] : 'default';
?>
<ul class="stark-subscribe stark-subscribe--<?php echo esc_attr( $variant ); ?>" aria-label="Listen and subscribe">
    <?php foreach ( stark_podcast_subscribe_links() as $link ) : ?>
        <li><a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener"><?php echo stark_podcast_platform_icon( $link['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?><span><?php echo esc_html( $link['label'] ); ?></span></a></li>
    <?php endforeach; ?>
</ul>
