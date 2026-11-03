<?php
/**
 * Who it's for — good fit / not a fit (v27.3.0, from the live pages).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$fit = $args['fit'] ?? null;
if ( empty( $fit['good'] ) && empty( $fit['bad'] ) ) return;
?>
<section class="stark-band stark-fit" aria-labelledby="stark-fit-title">
    <div class="stark-home__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow"><?php echo esc_html( $fit['eyebrow'] ); ?></p>
            <h2 id="stark-fit-title" class="stark-section-head__title"><?php echo esc_html( $fit['heading'] ?: 'Is this a fit?' ); ?></h2>
            <?php if ( $fit['intro'] ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $fit['intro'] ); ?></p><?php endif; ?>
        </header>
        <div class="stark-fit__grid">
            <?php foreach ( array( 'good' => 'yes', 'bad' => 'no' ) as $key => $mod ) :
                if ( empty( $fit[ $key ] ) ) continue; ?>
                <div class="stark-fit__col stark-fit__col--<?php echo esc_attr( $mod ); ?>">
                    <h3 class="stark-fit__heading"><?php echo esc_html( $fit[ $key . '_heading' ] ); ?></h3>
                    <ul class="stark-fit__list">
                        <?php foreach ( $fit[ $key ] as $point ) : ?><li><?php echo esc_html( $point ); ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
