<?php
/**
 * Service hero — eyebrow, H1, one-sentence thesis, two CTAs.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$ctx = $args;
?>
<section class="stark-hero stark-hero--service" aria-labelledby="stark-hero-title">
    <div class="stark-home__inner">
        <div class="stark-hero__content">
            <?php if ( $ctx['eyebrow'] ) : ?>
                <p class="stark-eyebrow stark-eyebrow--light"><?php echo esc_html( $ctx['eyebrow'] ); ?></p>
            <?php endif; ?>
            <h1 id="stark-hero-title" class="stark-hero__title"><?php echo esc_html( $ctx['title'] ); ?></h1>
            <?php if ( $ctx['thesis'] ) : ?>
                <p class="stark-hero__sub"><?php echo esc_html( $ctx['thesis'] ); ?></p>
            <?php endif; ?>
            <div class="stark-hero__ctas">
                <?php foreach ( array( 'cta_primary' => 'primary', 'cta_secondary' => 'ghost' ) as $key => $variant ) :
                    if ( ! $ctx[ $key ] ) continue; ?>
                    <a href="<?php echo esc_url( $ctx[ $key ]['url'] ); ?>" class="stark-btn stark-btn--<?php echo esc_attr( $variant ); ?>"<?php echo $ctx[ $key ]['target'] ? ' target="' . esc_attr( $ctx[ $key ]['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $ctx[ $key ]['title'] ); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
