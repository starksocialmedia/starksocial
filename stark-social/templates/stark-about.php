<?php
/**
 * Stark About page (v27.0.7)
 * Loaded for /about/ via inc/stark-pages.php. Content from
 * acf-json/group_stark_about.json; empty sections are skipped.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$id = get_queried_object_id();

$hero = array(
    'eyebrow'       => stark_field( 'hero_eyebrow', $id ) ?: 'About Stark Social',
    'title'         => stark_field( 'hero_title', $id ) ?: get_the_title( $id ),
    'thesis'        => stark_field( 'hero_intro', $id ),
    'cta_primary'   => stark_link( stark_field( 'hero_cta', $id ), array( 'url' => '/contact/', 'title' => 'Start a conversation' ) ),
    'cta_secondary' => null,
);

$story_heading = stark_field( 'story_heading', $id ) ?: 'Our story';
$story_body    = stark_field( 'story_body', $id );

$team = array_values( array_filter( (array) stark_field( 'team_members', $id ), function ( $row ) {
    return ! empty( $row['name'] );
} ) );

$values = array_values( array_filter( (array) stark_field( 'values', $id ), function ( $row ) {
    return ! empty( $row['title'] );
} ) );

$where_body = stark_field( 'where_body', $id );
$where_link = stark_link( stark_field( 'where_link', $id ), array( 'url' => '/service-areas/', 'title' => 'See our service areas' ) );

$cta = (array) stark_field( 'final_cta', $id );
$cta = array(
    'final_cta' => array(
        'title'  => ! empty( $cta['heading'] ) ? $cta['heading'] : 'Work with us',
        'sub'    => ! empty( $cta['subhead'] ) ? $cta['subhead'] : "Tell us what you're building. We'll tell you honestly whether we're the right fit.",
        'button' => stark_link( isset( $cta['button'] ) ? $cta['button'] : null, array( 'url' => '/contact/', 'title' => 'Get in touch' ) ),
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

<main id="main" class="stark-page stark-about">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <?php if ( $story_body ) : ?>
        <section class="stark-band stark-story" aria-labelledby="stark-story-title">
            <div class="stark-home__inner stark-story__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">Who we are</p>
                    <h2 id="stark-story-title" class="stark-section-head__title"><?php echo esc_html( $story_heading ); ?></h2>
                </header>
                <div class="stark-prose"><?php echo wp_kses_post( $story_body ); ?></div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( $team ) : ?>
        <section class="stark-band stark-team" aria-labelledby="stark-team-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">The team</p>
                    <h2 id="stark-team-title" class="stark-section-head__title"><?php echo esc_html( stark_field( 'team_heading', $id ) ?: 'Led by the people who do the work' ); ?></h2>
                </header>
                <ul class="stark-team__grid">
                    <?php foreach ( $team as $person ) :
                        $photo = isset( $person['photo'] ) ? $person['photo'] : null; ?>
                        <li class="stark-team-card">
                            <div class="stark-team-card__photo">
                                <?php if ( ! empty( $photo['ID'] ) ) :
                                    echo wp_get_attachment_image( $photo['ID'], 'medium_large', false, array( 'loading' => 'lazy', 'alt' => $person['name'] ) );
                                else : ?>
                                    <span class="stark-team-card__initials" aria-hidden="true"><?php echo esc_html( implode( '', array_map( function ( $w ) { return mb_substr( $w, 0, 1 ); }, array_slice( explode( ' ', $person['name'] ), 0, 2 ) ) ) ); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="stark-team-card__body">
                                <h3 class="stark-team-card__name"><?php echo esc_html( $person['name'] ); ?></h3>
                                <?php if ( ! empty( $person['role'] ) ) : ?>
                                    <p class="stark-team-card__role"><?php echo esc_html( $person['role'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $person['bio'] ) ) : ?>
                                    <p class="stark-team-card__bio"><?php echo esc_html( $person['bio'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( $values ) : ?>
        <section class="stark-band stark-values" aria-labelledby="stark-values-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">What we believe</p>
                    <h2 id="stark-values-title" class="stark-section-head__title"><?php echo esc_html( stark_field( 'values_heading', $id ) ?: 'Our values' ); ?></h2>
                </header>
                <ol class="stark-values__grid">
                    <?php foreach ( $values as $value ) : ?>
                        <li class="stark-value">
                            <h3 class="stark-value__title"><?php echo esc_html( $value['title'] ); ?></h3>
                            <?php if ( ! empty( $value['description'] ) ) : ?>
                                <p class="stark-value__desc"><?php echo esc_html( $value['description'] ); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
    <?php endif; ?>

    <?php $awards = function_exists( 'stark_get_awards' ) ? stark_get_awards() : array(); if ( $awards ) : ?>
        <section class="stark-band stark-recognition" aria-labelledby="stark-recognition-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">Recognition</p>
                    <h2 id="stark-recognition-title" class="stark-section-head__title">Awards</h2>
                </header>
                <ul class="stark-cards stark-cards--<?php echo esc_attr( min( 3, count( $awards ) ) ); ?>">
                    <?php foreach ( $awards as $award ) : $url = stark_award_url( $award ); ?>
                        <li class="stark-cards__item">
                            <p class="stark-eyebrow"><?php echo esc_html( $award['year'] ); ?></p>
                            <h3 class="stark-cards__title"><?php echo $url ? '<a href="' . esc_url( $url ) . '">' . esc_html( $award['title'] ) . '</a>' : esc_html( $award['title'] ); ?></h3>
                            <p class="stark-cards__desc"><?php echo esc_html( $award['org'] . ( $award['for'] ? ' · ' . $award['for'] : '' ) ); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <section class="stark-band stark-where" aria-labelledby="stark-where-title">
        <div class="stark-home__inner stark-where__inner">
            <div>
                <p class="stark-eyebrow">Where we work</p>
                <h2 id="stark-where-title" class="stark-section-head__title"><?php echo esc_html( stark_field( 'where_heading', $id ) ?: 'Southern California, and beyond' ); ?></h2>
                <?php if ( $where_body ) : ?>
                    <p class="stark-where__body"><?php echo esc_html( $where_body ); ?></p>
                <?php endif; ?>
                <?php if ( $where_link ) : ?>
                    <a class="stark-case__link" href="<?php echo esc_url( $where_link['url'] ); ?>"><?php echo esc_html( $where_link['title'] ); ?> &rarr;</a>
                <?php endif; ?>
            </div>
            <ul class="stark-cred__areas stark-where__markets">
                <?php foreach ( stark_get_markets() as $market ) : ?>
                    <li><?php echo esc_html( $market['name'] ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
