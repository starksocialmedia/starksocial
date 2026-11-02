<?php
/**
 * Stark blog archive (v27.0.9)
 * Posts index (/blog/) plus category, tag, author and date archives.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

list( $eyebrow, $title, $intro ) = stark_blog_archive_heading();

$hero = array(
    'eyebrow'       => $eyebrow,
    'title'         => $title,
    'thesis'        => $intro ? wp_strip_all_tags( $intro ) : '',
    'cta_primary'   => null,
    'cta_secondary' => null,
);

$categories = get_categories( array( 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_category' ) ) ) );
$current    = is_category() ? get_queried_object_id() : 0;
$blog_url   = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );

$cta = array(
    'final_cta' => array(
        'title'  => 'Want this done for you?',
        'sub'    => "Reading about marketing is the easy part. We'll handle the doing.",
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

<main id="main" class="stark-page stark-blog">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <section class="stark-band stark-blog__list" aria-label="Posts">
        <div class="stark-home__inner">

            <?php if ( $categories && ( is_home() || is_category() ) ) : ?>
                <nav class="stark-blog__cats" aria-label="Blog categories">
                    <ul>
                        <li><a href="<?php echo esc_url( $blog_url ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>>All</a></li>
                        <?php foreach ( $categories as $cat ) : ?>
                            <li><a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"<?php echo $current === $cat->term_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $cat->name ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <?php if ( have_posts() ) : ?>
                <div class="stark-post-grid">
                    <?php while ( have_posts() ) : the_post(); get_template_part( 'templates/parts/blog', 'card' ); endwhile; ?>
                </div>

                <?php
                $pagination = paginate_links( array(
                    'type'      => 'list',
                    'mid_size'  => 1,
                    'prev_text' => '<span aria-hidden="true">&larr;</span> Newer',
                    'next_text' => 'Older <span aria-hidden="true">&rarr;</span>',
                ) );
                if ( $pagination ) : ?>
                    <nav class="stark-pagination" aria-label="Posts pagination"><?php echo wp_kses_post( $pagination ); ?></nav>
                <?php endif; ?>
            <?php else : ?>
                <p class="stark-blog__empty">No posts here yet.</p>
            <?php endif; ?>

        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
