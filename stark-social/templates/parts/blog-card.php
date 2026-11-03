<?php
/**
 * Post card — featured image, category tag, title, excerpt, date, author.
 * Used by the blog archive, related posts and search results.
 * $args: WP_Post or post ID; defaults to the current post in the loop.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$post_obj = get_post( ! empty( $args ) ? $args : null );
$cat      = stark_primary_category( $post_obj );
?>
<article class="stark-post-card">
    <div class="stark-post-card__media">
        <?php if ( has_post_thumbnail( $post_obj ) ) :
            // First row of the main archive loop is above the fold: load it eagerly (LCP).
            global $wp_query;
            $above_fold = in_the_loop() && $wp_query->current_post < 3;
            echo get_the_post_thumbnail( $post_obj, 'medium_large', array(
                'loading'       => $above_fold ? 'eager' : 'lazy',
                'fetchpriority' => $above_fold && 0 === $wp_query->current_post ? 'high' : 'auto',
                'sizes'         => '(min-width: 1024px) 420px, (min-width: 640px) 50vw, 100vw',
                'alt'           => '',
            ) );
        endif; ?>
    </div>
    <div class="stark-post-card__body">
        <?php if ( $cat ) : ?>
            <p class="stark-post-card__cat"><?php echo esc_html( $cat->name ); ?></p>
        <?php endif; ?>
        <h3 class="stark-post-card__title"><a href="<?php echo esc_url( get_permalink( $post_obj ) ); ?>"><?php echo esc_html( get_the_title( $post_obj ) ); ?></a></h3>
        <?php
        // Excerpt filters (Podlove's among them) read the global post, which is
        // empty on 404 and search pages: point it at this card while they run.
        global $post;
        $prev_post = $post;
        $post      = $post_obj; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- restored below
        setup_postdata( $post );
        $excerpt = wp_trim_words( get_the_excerpt( $post_obj ), 24, '…' );
        $post = $prev_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
        if ( $prev_post ) setup_postdata( $prev_post ); else wp_reset_postdata();
        ?>
        <p class="stark-post-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <p class="stark-post-card__meta">
            <time datetime="<?php echo esc_attr( get_the_date( 'c', $post_obj ) ); ?>"><?php echo esc_html( get_the_date( '', $post_obj ) ); ?></time>
            <span aria-hidden="true">·</span>
            <span><?php echo esc_html( get_the_author_meta( 'display_name', $post_obj->post_author ) ); ?></span>
        </p>
    </div>
</article>
