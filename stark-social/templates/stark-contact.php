<?php
/**
 * Stark Contact page (v27.0.8)
 * Loaded for /contact/ via inc/stark-pages.php. Content from
 * acf-json/group_stark_contact.json. Form is a Gravity Forms shortcode;
 * a placeholder shows until the configured form ID exists.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$id = get_queried_object_id();

$hero = array(
    'eyebrow'       => stark_field( 'hero_eyebrow', $id ) ?: 'Get in touch',
    'title'         => stark_field( 'hero_title', $id ) ?: "Let's talk about your growth.",
    'thesis'        => stark_field( 'hero_intro', $id ),
    'cta_primary'   => null,
    'cta_secondary' => null,
);

$form_id = (int) ( stark_field( 'gf_form_id', $id ) ?: 1 );
$form_ok = class_exists( 'GFAPI' ) && GFAPI::get_form( $form_id );

// Defaults mirror the live Contact page.
$address = stark_lines( stark_field( 'address', $id ) ?: "P.O. Box 55081\nValencia, CA 91385-0081" );
$phone   = stark_field( 'phone', $id ) ?: '(661) 513-3996';
$tel     = preg_replace( '/[^0-9+]/', '', $phone );
$tel     = 10 === strlen( $tel ) ? '+1' . $tel : $tel; // US numbers without country code
$email   = stark_field( 'email', $id ) ?: 'hello@starksocial.com';
$hours   = array_values( array_filter( (array) stark_field( 'hours', $id ), function ( $row ) {
    return ! empty( $row['days'] );
} ) ) ?: array(
    array( 'days' => 'Mon–Thu', 'time' => '9:00 AM – 5:00 PM' ),
    array( 'days' => 'Friday', 'time' => '9:00 AM – 4:00 PM' ),
);
$social = array_values( array_filter( (array) stark_field( 'social', $id ), function ( $row ) {
    return ! empty( $row['url'] );
} ) ) ?: stark_default_social_links();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-contact">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <section class="stark-band stark-contact__band" aria-label="Contact form and details">
        <div class="stark-home__inner stark-contact__grid">

            <div class="stark-contact__form">
                <h2 class="stark-contact__heading"><?php echo esc_html( stark_field( 'form_heading', $id ) ?: 'Tell us about your business' ); ?></h2>
                <?php $form_intro = stark_field( 'form_intro', $id ); if ( $form_intro ) : ?>
                    <p class="stark-contact__intro"><?php echo esc_html( $form_intro ); ?></p>
                <?php endif; ?>

                <?php if ( $form_ok ) : ?>
                    <?php echo do_shortcode( '[gravityform id="' . $form_id . '" title="false" description="false" ajax="true"]' ); ?>
                <?php else : ?>
                    <div class="stark-contact__placeholder">
                        <p><strong>Form placeholder.</strong> Gravity Forms form ID <?php echo esc_html( $form_id ); ?> doesn't exist yet. Create it in Forms, or change the ID on this page's Form tab.</p>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="stark-contact__details" aria-labelledby="stark-contact-details-title">
                <h2 id="stark-contact-details-title" class="stark-contact__heading stark-contact__heading--sm">Contact details</h2>
                <dl class="stark-contact__list">
                    <?php if ( $phone ) : ?>
                        <div class="stark-contact__item">
                            <dt>Phone</dt>
                            <dd><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $phone ); ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ( $email ) : ?>
                        <div class="stark-contact__item">
                            <dt>Email</dt>
                            <dd><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ( $address ) : ?>
                        <div class="stark-contact__item">
                            <dt>Mailing address</dt>
                            <dd><address><?php echo implode( '<br>', array_map( 'esc_html', $address ) ); ?></address></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ( $hours ) : ?>
                        <div class="stark-contact__item">
                            <dt>Office hours</dt>
                            <dd>
                                <ul class="stark-contact__hours">
                                    <?php foreach ( $hours as $row ) : ?>
                                        <li><span><?php echo esc_html( $row['days'] ); ?></span> <span><?php echo esc_html( $row['time'] ); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            </dd>
                        </div>
                    <?php endif; ?>
                </dl>

                <?php if ( $social ) : ?>
                    <ul class="stark-contact__social" aria-label="Social media">
                        <?php foreach ( $social as $row ) : $icon = stark_social_icon( $row['network'] ); if ( ! $icon ) continue; ?>
                            <li><a href="<?php echo esc_url( $row['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $row['network'] ) ); ?>"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </aside>

        </div>
    </section>
</main>

<?php wp_footer(); ?>
</body>
</html>
