<?php
/**
 * From the blog — up to 3 posts most about this service (v27.4.3,
 * stark_service_related_posts()). Nothing when no post matches.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( empty( $args['blog_posts'] ) ) return;
?>
<section class="stark-band stark-related-posts stark-service-blog" aria-labelledby="stark-service-blog-title">
    <div class="stark-home__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow">From the blog</p>
            <h2 id="stark-service-blog-title" class="stark-section-head__title">Reading on <?php echo esc_html( $args['service_name'] ); ?></h2>
        </header>
        <div class="stark-post-grid">
            <?php foreach ( $args['blog_posts'] as $p ) get_template_part( 'templates/parts/blog', 'card', $p ); ?>
        </div>
    </div>
</section>
