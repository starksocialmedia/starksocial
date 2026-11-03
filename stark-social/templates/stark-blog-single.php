<?php
/**
 * Stark blog single post (v27.0.9)
 * Hero with meta row → featured image → body (.stark-prose) → author box →
 * related posts → CTA band.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

the_post();

$post_id   = get_the_ID();
$author_id = (int) get_post_field( 'post_author', $post_id );
$cat       = stark_primary_category( $post_id );
$related   = stark_related_posts( $post_id );

$cta = array(
    'final_cta' => array(
        'title'  => 'Want help putting this to work?',
        'sub'    => "We'll look at your business and tell you honestly what would move the needle.",
        'button' => array( 'url' => '/contact/', 'title' => 'Get in touch', 'target' => '' ),
    ),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-article">
    <article <?php post_class( 'stark-article__post' ); ?>>

        <header class="stark-hero stark-hero--article">
            <div class="stark-home__inner">
                <div class="stark-hero__content stark-article__head">
                    <?php if ( $cat ) : ?>
                        <p class="stark-eyebrow stark-eyebrow--light"><a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></p>
                    <?php endif; ?>
                    <h1 class="stark-hero__title stark-article__title"><?php the_title(); ?></h1>
                    <p class="stark-article__meta">
                        <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></a>
                        <span aria-hidden="true">·</span>
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                        <span aria-hidden="true">·</span>
                        <span><?php echo esc_html( stark_reading_time( $post_id ) ); ?> min read</span>
                    </p>
                </div>
            </div>
        </header>
        <?php get_template_part( 'templates/parts/breadcrumbs' ); ?>

        <?php if ( has_post_thumbnail() ) : ?>
            <figure class="stark-article__image">
                <?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high', 'loading' => 'eager' ) ); ?>
            </figure>
        <?php endif; ?>

        <div class="stark-article__body stark-prose">
            <?php the_content(); ?>
        </div>

        <?php if ( stark_show_author_box( $author_id ) ) :
            $bio = get_the_author_meta( 'description', $author_id ); ?>
            <aside class="stark-author" aria-label="About the author">
                <div class="stark-author__avatar"><?php echo stark_author_avatar( $author_id, 72 ); // phpcs:ignore WordPress.Security.EscapeOutput -- core/attachment markup ?></div>
                <div>
                    <p class="stark-author__label">Written by</p>
                    <p class="stark-author__name"><a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></a></p>
                    <?php $role = stark_field( 'stark_role', 'user_' . $author_id ); if ( $role ) : ?>
                        <p class="stark-author__role"><?php echo esc_html( $role ); ?></p>
                    <?php endif; ?>
                    <?php if ( $bio ) : ?>
                        <p class="stark-author__bio"><?php echo esc_html( wp_trim_words( $bio, 32, '…' ) ); ?></p>
                    <?php endif; ?>
                    <a class="stark-author__link" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">View profile and posts &rarr;</a>
                </div>
            </aside>
        <?php endif; ?>

    </article>

    <?php if ( $related ) : ?>
        <section class="stark-band stark-related-posts" aria-labelledby="stark-related-posts-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">Keep reading</p>
                    <h2 id="stark-related-posts-title" class="stark-section-head__title">Related posts</h2>
                </header>
                <div class="stark-post-grid">
                    <?php foreach ( $related as $rel ) get_template_part( 'templates/parts/blog', 'card', $rel ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
