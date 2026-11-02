<?php
/**
 * Case study teaser(s) — one featured on service pages, up to three local
 * on location pages. Each links out to the full case study.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! $args['case_studies'] ) return;
$multiple = count( $args['case_studies'] ) > 1;
?>
<section class="stark-band stark-case" aria-labelledby="stark-case-title">
    <div class="stark-home__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow">Case study</p>
            <h2 id="stark-case-title" class="stark-section-head__title"><?php echo esc_html( $args['case_heading'] ); ?></h2>
        </header>
        <div class="stark-case__list<?php echo $multiple ? ' stark-case__list--multi' : ''; ?>">
            <?php foreach ( $args['case_studies'] as $case ) :
                $link = stark_link( isset( $case['link'] ) ? $case['link'] : null );
                $image = isset( $case['image'] ) ? $case['image'] : null; ?>
                <article class="stark-case__card">
                    <?php if ( ! empty( $image['ID'] ) ) : ?>
                        <div class="stark-case__media"><?php echo wp_get_attachment_image( $image['ID'], 'large', false, array( 'loading' => 'lazy' ) ); ?></div>
                    <?php endif; ?>
                    <div class="stark-case__body">
                        <?php if ( ! empty( $case['client'] ) ) : ?>
                            <p class="stark-case__client"><?php echo esc_html( $case['client'] ); ?></p>
                        <?php endif; ?>
                        <h3 class="stark-case__headline"><?php echo esc_html( $case['headline'] ); ?></h3>
                        <?php if ( ! empty( $case['result_value'] ) ) : ?>
                            <p class="stark-case__stat"><strong><?php echo esc_html( $case['result_value'] ); ?></strong> <span><?php echo esc_html( $case['result_label'] ); ?></span></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $case['summary'] ) ) : ?>
                            <p class="stark-case__summary"><?php echo esc_html( $case['summary'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( $link ) : ?>
                            <a class="stark-case__link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( 'Learn more' === $link['title'] ? 'Read the case study' : $link['title'] ); ?> &rarr;</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
