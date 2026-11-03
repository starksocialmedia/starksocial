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
            <?php
            // v27.5.6: optional 'title_accent' — a phrase in the title set in the accent colour.
            $title_html = esc_html( $ctx['title'] );
            if ( ! empty( $ctx['title_accent'] ) && false !== strpos( $ctx['title'], $ctx['title_accent'] ) ) {
                $title_html = str_replace( esc_html( $ctx['title_accent'] ), '<span class="stark-accent">' . esc_html( $ctx['title_accent'] ) . '</span>', $title_html );
            }
            ?>
            <h1 id="stark-hero-title" class="stark-hero__title"><?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?></h1>
            <?php if ( $ctx['thesis'] ) : ?>
                <p class="stark-hero__sub"><?php echo esc_html( $ctx['thesis'] ); ?></p>
            <?php endif; ?>
            <div class="stark-hero__ctas">
                <?php foreach ( array( 'cta_primary' => 'primary', 'cta_secondary' => 'ghost' ) as $key => $variant ) :
                    if ( ! $ctx[ $key ] ) continue; ?>
                    <a href="<?php echo esc_url( $ctx[ $key ]['url'] ); ?>" class="stark-btn stark-btn--<?php echo esc_attr( $variant ); ?>"<?php echo $ctx[ $key ]['target'] ? ' target="' . esc_attr( $ctx[ $key ]['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $ctx[ $key ]['title'] ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php if ( ! empty( $ctx['hero_note'] ) ) : ?>
                <p class="stark-hero__note"><?php echo esc_html( $ctx['hero_note'] ); ?></p>
            <?php endif; ?>
        </div>
        <?php if ( ! empty( $ctx['service_slug'] ) && ( $glyph = stark_service_glyph( $ctx['service_slug'], 'stark-hero__glyph-svg' ) ) ) : ?>
            <span class="stark-hero__glyph" aria-hidden="true"><?php echo $glyph; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
        <?php endif; ?>
    </div>
</section>
