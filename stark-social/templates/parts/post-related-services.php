<?php
/**
 * Related services row on blog posts (v27.4.5): the services a post is about
 * (inc/stark-topics.php), as a quiet list of links. Nothing when none match.
 * $args: array of [slug, name, desc, url].
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( empty( $args ) ) return;
?>
<nav class="stark-post-services" aria-labelledby="stark-post-services-title">
    <h2 id="stark-post-services-title" class="stark-post-services__title">Related services</h2>
    <ul class="stark-post-services__list">
        <?php foreach ( $args as $svc ) : ?>
            <li class="stark-post-services__item">
                <a class="stark-post-services__link" href="<?php echo esc_url( $svc['url'] ); ?>"><?php echo esc_html( $svc['name'] ); ?> <span aria-hidden="true">&rarr;</span></a>
                <span class="stark-post-services__desc"><?php echo esc_html( $svc['desc'] ); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
