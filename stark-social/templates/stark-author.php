<?php
/**
 * Stark author page (v27.0.17)
 * Hero (photo, name, role, highlights, bio) → stats → latest posts →
 * latest episodes → follow / work with links → CTA.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$author  = get_queried_object();
$profile = stark_author_profile( $author->ID );

$latest_posts = get_posts( array( 'author' => $author->ID, 'numberposts' => 3 ) );
$latest_eps   = get_posts( $profile['host']
    ? array( 'post_type' => 'podcast', 'numberposts' => 3 )
    : array( 'post_type' => 'podcast', 'numberposts' => 3, 'author' => $author->ID ) );

$stats = array_filter( array(
    array( $profile['posts'], 1 === $profile['posts'] ? 'Article published' : 'Articles published' ),
    $profile['episodes'] ? array( $profile['episodes'], 1 === $profile['episodes'] ? 'Podcast episode' : 'Podcast episodes' ) : null,
    $profile['years'] ? array( $profile['years'], 'Years at Stark' ) : null,
) );

$cta = array(
    'final_cta' => array(
        'title'  => 'Work with ' . $profile['first'],
        'sub'    => "Tell us what you're working on. You'll hear back from the people who do the work.",
        'button' => array( 'url' => '/contact/', 'title' => 'Start a conversation', 'target' => '' ),
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

<main id="main" class="stark-page stark-profile">
    <header class="stark-hero stark-hero--profile">
        <div class="stark-home__inner stark-profile__head">
            <div class="stark-profile__photo">
                <?php if ( $profile['photo_id'] ) :
                    echo wp_get_attachment_image( $profile['photo_id'], 'medium_large', false, array( 'alt' => $profile['name'], 'fetchpriority' => 'high', 'loading' => 'eager' ) );
                else : ?>
                    <span class="stark-team-card__initials" aria-hidden="true"><?php echo esc_html( implode( '', array_map( function ( $w ) { return mb_substr( $w, 0, 1 ); }, array_slice( explode( ' ', $profile['name'] ), 0, 2 ) ) ) ); ?></span>
                <?php endif; ?>
            </div>
            <div class="stark-profile__intro">
                <p class="stark-eyebrow stark-eyebrow--light"><a href="/about/">About Stark Social</a></p>
                <h1 class="stark-hero__title stark-profile__name"><?php echo esc_html( $profile['name'] ); ?></h1>
                <?php if ( $profile['role'] ) : ?>
                    <p class="stark-profile__role"><?php echo esc_html( $profile['role'] ); ?></p>
                <?php endif; ?>
                <?php if ( $profile['highlights'] ) : ?>
                    <ul class="stark-profile__highlights">
                        <?php foreach ( $profile['highlights'] as $h ) : ?><li><?php echo esc_html( $h ); ?></li><?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if ( $profile['bio_lead'] ) : ?>
                    <div class="stark-profile__bio"><?php echo wp_kses_post( wpautop( $profile['bio_lead'] ) ); ?></div>
                <?php endif; ?>
                <ul class="stark-profile__links">
                    <li><a class="stark-btn stark-btn--primary" href="/contact/">Work with <?php echo esc_html( $profile['first'] ); ?></a></li>
                    <?php foreach ( array( 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram' ) as $key => $label ) :
                        if ( empty( $profile[ $key ] ) ) continue; ?>
                        <li><a class="stark-btn stark-btn--ghost" href="<?php echo esc_url( $profile[ $key ] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </header>

    <?php if ( $stats ) : ?>
        <div class="stark-home__inner">
            <dl class="stark-case-study__stats stark-case-study__stats--<?php echo esc_attr( count( $stats ) ); ?> stark-profile__stats">
                <?php foreach ( $stats as $stat ) : ?>
                    <div class="stark-case-study__stat">
                        <dt><?php echo esc_html( $stat[1] ); ?></dt>
                        <dd><?php echo esc_html( number_format_i18n( $stat[0] ) ); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    <?php endif; ?>

    <?php if ( $profile['bio_rest'] || $profile['focus'] ) : ?>
        <section class="stark-band stark-profile__background" aria-labelledby="stark-profile-bg-title">
            <div class="stark-home__inner stark-profile__background-inner">
                <?php if ( $profile['bio_rest'] ) : ?>
                    <div>
                        <p class="stark-eyebrow">Background</p>
                        <h2 id="stark-profile-bg-title" class="stark-section-head__title">About <?php echo esc_html( $profile['first'] ); ?></h2>
                        <div class="stark-prose"><?php echo wp_kses_post( wpautop( $profile['bio_rest'] ) ); ?></div>
                    </div>
                <?php else : ?>
                    <h2 id="stark-profile-bg-title" class="screen-reader-text">About <?php echo esc_html( $profile['first'] ); ?></h2>
                <?php endif; ?>
                <?php if ( $profile['focus'] ) : ?>
                    <div class="stark-profile__focus">
                        <p class="stark-eyebrow">Focus</p>
                        <h3 class="stark-profile__focus-title"><?php echo esc_html( $profile['focus_heading'] ?: 'How ' . $profile['first'] . ' works' ); ?></h3>
                        <?php if ( $profile['focus_intro'] ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $profile['focus_intro'] ); ?></p><?php endif; ?>
                        <ul class="stark-cards stark-cards--2">
                            <?php foreach ( $profile['focus'] as $item ) : ?>
                                <li class="stark-cards__item"><h4 class="stark-cards__title"><?php echo esc_html( $item['title'] ); ?></h4><?php if ( $item['description'] ) : ?><p class="stark-cards__desc"><?php echo esc_html( $item['description'] ); ?></p><?php endif; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( $latest_posts ) : ?>
        <section class="stark-band stark-profile__posts" aria-labelledby="stark-profile-posts-title">
            <div class="stark-home__inner">
                <header class="stark-section-head stark-section-head--row">
                    <div>
                        <p class="stark-eyebrow">Writing</p>
                        <h2 id="stark-profile-posts-title" class="stark-section-head__title">Latest from <?php echo esc_html( $profile['first'] ); ?></h2>
                    </div>
                    <?php if ( $profile['posts'] > 3 ) : ?>
                        <a class="stark-case__link" href="<?php echo esc_url( add_query_arg( 'view', 'posts', get_author_posts_url( $author->ID ) ) ); ?>">All <?php echo esc_html( number_format_i18n( $profile['posts'] ) ); ?> articles &rarr;</a>
                    <?php endif; ?>
                </header>
                <div class="stark-post-grid">
                    <?php foreach ( $latest_posts as $item ) get_template_part( 'templates/parts/blog', 'card', $item ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( $latest_eps ) : ?>
        <section class="stark-band stark-profile__episodes" aria-labelledby="stark-profile-eps-title">
            <div class="stark-home__inner">
                <header class="stark-section-head stark-section-head--row">
                    <div>
                        <p class="stark-eyebrow">The Social Life Podcast</p>
                        <h2 id="stark-profile-eps-title" class="stark-section-head__title">Latest episodes</h2>
                    </div>
                    <a class="stark-case__link" href="<?php echo esc_url( get_post_type_archive_link( 'podcast' ) ); ?>">All episodes &rarr;</a>
                </header>
                <div class="stark-episode-grid">
                    <?php foreach ( $latest_eps as $item ) get_template_part( 'templates/parts/podcast', 'card', $item ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
