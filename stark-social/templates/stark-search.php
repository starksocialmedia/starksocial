<?php
/**
 * Stark search results (v27.0.10)
 * Results list (all post types), pagination, empty state with suggestions.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $wp_query;
$query = get_search_query();
$total = (int) $wp_query->found_posts;
$recent = $total ? array() : get_posts( array( 'numberposts' => 3, 'suppress_filters' => false ) );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-search">
    <section class="stark-hero stark-hero--search" aria-labelledby="stark-search-title">
        <div class="stark-home__inner">
            <div class="stark-hero__content">
                <p class="stark-eyebrow stark-eyebrow--light">Search</p>
                <h1 id="stark-search-title" class="stark-hero__title stark-search__title">
                    <?php if ( '' === $query ) : ?>
                        Search the site
                    <?php else : ?>
                        Results for &ldquo;<?php echo esc_html( $query ); ?>&rdquo;
                    <?php endif; ?>
                </h1>
                <?php if ( '' !== $query ) : ?>
                    <p class="stark-hero__sub"><?php echo esc_html( sprintf( _n( '%s result', '%s results', $total ), number_format_i18n( $total ) ) ); ?></p>
                <?php endif; ?>
                <?php echo stark_search_form( 'light', 'stark-search-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper ?>
            </div>
        </div>
    </section>

    <section class="stark-band stark-search__results" aria-label="Search results">
        <div class="stark-home__inner stark-search__inner">
            <?php if ( have_posts() && '' !== $query ) : ?>
                <ol class="stark-results">
                    <?php while ( have_posts() ) : the_post();
                        $type = get_post_type_object( get_post_type() );
                        $label = 'post' === get_post_type() ? ( stark_primary_category() ? stark_primary_category()->name : 'Blog' ) : $type->labels->singular_name; ?>
                        <li class="stark-result">
                            <p class="stark-result__type"><?php echo esc_html( $label ); ?></p>
                            <h2 class="stark-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="stark-result__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
                            <p class="stark-result__url"><?php echo esc_html( wp_parse_url( get_permalink(), PHP_URL_PATH ) ); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ol>

                <?php
                $pagination = paginate_links( array(
                    'type'      => 'list',
                    'mid_size'  => 1,
                    'prev_text' => '<span aria-hidden="true">&larr;</span> Previous',
                    'next_text' => 'Next <span aria-hidden="true">&rarr;</span>',
                ) );
                if ( $pagination ) : ?>
                    <nav class="stark-pagination" aria-label="Search results pagination"><?php echo wp_kses_post( $pagination ); ?></nav>
                <?php endif; ?>

            <?php else : ?>
                <div class="stark-search__empty">
                    <h2 class="stark-section-head__title"><?php echo '' === $query ? 'What are you looking for?' : 'Nothing matched that search.'; ?></h2>
                    <ul class="stark-search__tips">
                        <li>Check the spelling, or try a shorter, broader term.</li>
                        <li>Search a topic instead of a phrase: <em>SEO</em>, <em>passwords</em>, <em>Instagram</em>.</li>
                        <li>Or <a href="/contact/">ask us directly</a>. We answer every message.</li>
                    </ul>
                    <nav class="stark-404__links stark-404__links--dark" aria-label="Popular pages">
                        <p class="stark-404__links-label">Popular pages</p>
                        <ul>
                            <?php foreach ( stark_popular_links() as $link ) : ?>
                                <li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ( $recent ) : ?>
        <section class="stark-band" aria-labelledby="stark-search-recent-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">Recent posts</p>
                    <h2 id="stark-search-recent-title" class="stark-section-head__title">Start here instead</h2>
                </header>
                <div class="stark-post-grid">
                    <?php foreach ( $recent as $post_item ) get_template_part( 'templates/parts/blog', 'card', $post_item ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
